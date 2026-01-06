<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = (string) config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        // Optional: kalau kamu set notif URL di config/services.php
        // Midtrans akan kirim webhook ke sini.
        $notifUrl = config('services.midtrans.notification_url');
        if (! empty($notifUrl)) {
            // Beberapa versi midtrans-php beda properti, set dua-duanya aman.
            Config::$overrideNotifUrl = $notifUrl;
            Config::$appendNotifUrl   = $notifUrl;
        }
    }

    /**
     * Buat snap token untuk UPGRADE SUBSCRIPTION (billing).
     */
    public function createSubscriptionSnapToken(SubscriptionPayment $payment): string
    {
        // Ambil user yang paling masuk akal (creator checkout / owner store)
        // Sesuaikan relasi kalau modelmu sudah punya.
        $userName  = 'Customer';
        $userEmail = null;

        try {
            // Kalau model SubscriptionPayment punya relasi ->subscription->store->owner, pakai itu.
            if (method_exists($payment, 'subscription') && $payment->relationLoaded('subscription')) {
                // do nothing, sudah ke-load
            }

            // Best effort: coba cari owner store dari subscription
            $subscription = $payment->subscription ?? null;
            $store = $subscription?->store ?? null;
            $owner = $store?->owner ?? null;

            if ($owner) {
                $userName  = $owner->name ?? $userName;
                $userEmail = $owner->email ?? $userEmail;
            } else {
                // fallback: auth()
                $userName  = auth()->user()?->name ?? $userName;
                $userEmail = auth()->user()?->email ?? $userEmail;
            }
        } catch (\Throwable $e) {
            // fallback ke auth(), jangan bikin gagal token
            $userName  = auth()->user()?->name ?? $userName;
            $userEmail = auth()->user()?->email ?? $userEmail;
        }

        $payload = [
            'transaction_details' => [
                'order_id'     => (string) $payment->order_id,
                'gross_amount' => (int) $payment->amount,
            ],
            'item_details' => [
                [
                    'id'       => 'plan-pro',
                    'price'    => (int) $payment->amount,
                    'quantity' => 1,
                    'name'     => 'Upgrade Paket Pro',
                ],
            ],
            'customer_details' => [
                'first_name' => mb_substr((string) $userName, 0, 50),
                'email'      => $userEmail,
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minute',
                'duration'   => 30,
            ],
        ];

        try {
            return Snap::getSnapToken($payload);
        } catch (\Throwable $e) {
            Log::error('MIDTRANS SUBSCRIPTION SNAP TOKEN FAILED', [
                'payment_id' => $payment->id ?? null,
                'order_id'   => $payment->order_id ?? null,
                'amount'     => $payment->amount ?? null,
                'payload'    => $payload,
                'error'      => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal membuat pembayaran Midtrans (subscription): ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Buat snap token untuk TRANSAKSI KASIR.
     */
    public function createSnapToken(Transaction $transaction): string
    {
        $transaction->loadMissing('items');

        $payload = [
            'transaction_details' => [
                'order_id'     => (string) $transaction->invoice_number,
                'gross_amount' => (int) round($transaction->total_amount),
            ],
            'item_details' => $transaction->items->map(function ($item) {
                return [
                    'id'       => (string) ($item->product_variant_id ?: $item->product_id),
                    'price'    => (int) round($item->price),
                    'quantity' => (int) $item->qty,
                    'name'     => mb_substr((string) $item->product_name_snapshot, 0, 50),
                ];
            })->values()->all(),
            'customer_details' => [
                'first_name' => mb_substr((string) ($transaction->customer_name ?: 'Customer'), 0, 50),
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minute',
                'duration'   => 30,
            ],
        ];

        try {
            return Snap::getSnapToken($payload);
        } catch (\Throwable $e) {
            Log::error('MIDTRANS SNAP TOKEN FAILED', [
                'invoice_number' => $transaction->invoice_number,
                'payload'        => $payload,
                'error'          => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal membuat Snap Token Midtrans: ' . $e->getMessage(), 0, $e);
        }
    }
}
