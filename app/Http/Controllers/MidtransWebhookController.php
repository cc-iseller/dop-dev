<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('MIDTRANS WEBHOOK MASUK', $request->all());

        /**
         * ===============================
         * VALIDASI SIGNATURE (WAJIB)
         * ===============================
         */
        $serverKey = config('services.midtrans.server_key');

        $expectedSignature = hash(
            'sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($expectedSignature !== $request->signature_key) {
            Log::error('MIDTRANS SIGNATURE INVALID');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        /**
         * ===============================
         * AMBIL TRANSAKSI
         * ===============================
         */
        $transaction = Transaction::with('items')->where(
            'invoice_number',
            $request->order_id
        )->first();

        if (! $transaction) {
            Log::error('TRANSAKSI TIDAK DITEMUKAN', [
                'order_id' => $request->order_id,
            ]);

            return response()->json(['message' => 'Transaction not found'], 404);
        }

        DB::transaction(function () use ($request, $transaction) {

            /**
             * ===============================
             * STATUS BERHASIL
             * ===============================
             */
            if (in_array($request->transaction_status, ['settlement', 'capture'])) {

                // ⛔ Anti double webhook
                if ($transaction->status === 'paid') {
                    return;
                }

                // ✅ UPDATE TRANSAKSI
                $transaction->update([
                    'status' => 'paid',
                    'payment_method' => $this->mapPaymentMethod($request),
                    'midtrans_transaction_id' => $request->transaction_id,
                    'midtrans_payment_type' => $request->payment_type,
                    'midtrans_transaction_status' => $request->transaction_status,
                    'midtrans_fraud_status' => $request->fraud_status ?? null,
                    'midtrans_response' => $request->all(),
                ]);

                // 🔥 KURANGI STOK
                foreach ($transaction->items as $item) {
                    if ($item->product_variant_id) {
                        ProductVariant::where('id', $item->product_variant_id)
                            ->decrement('stock', $item->qty);
                    } else {
                        Product::where('id', $item->product_id)
                            ->decrement('base_stock', $item->qty);
                    }
                }
            }

            /**
             * ===============================
             * STATUS GAGAL / EXPIRE
             * ===============================
             */
            if (in_array($request->transaction_status, ['expire', 'cancel', 'deny'])) {
                $transaction->update([
                    'status' => 'cancelled',
                    'midtrans_transaction_status' => $request->transaction_status,
                    'midtrans_response' => $request->all(),
                ]);
            }
        });

        // ⚠️ MIDTRANS WAJIB TERIMA 200
        return response()->json(['status' => 'ok']);
    }

    /**
     * ===============================
     * MAP METODE PEMBAYARAN
     * Mapping ke ENUM: cash, debit, transfer, qris, midtrans
     * ===============================
     */
    private function mapPaymentMethod(Request $request): string
    {
        return match ($request->payment_type) {
            'qris' => 'qris',
            'bank_transfer', 'echannel', 'permata' => 'transfer',
            'gopay', 'shopeepay' => 'qris',
            'credit_card', 'cstore', 'akulaku', 'kredivo' => 'midtrans',
            default => 'midtrans',
        };
    }

    /**
     * ===============================
     * GET PAYMENT DETAIL (untuk display)
     * Format: "Transfer Bank - BNI"
     * ===============================
     */
    public static function getPaymentDetail(Transaction $transaction): string
    {
        // Untuk pembayaran cash
        if ($transaction->payment_method === 'cash') {
            return 'Tunai (Cash)';
        }

        // Untuk pembayaran debit
        if ($transaction->payment_method === 'debit') {
            return 'Kartu Debit';
        }

        // Untuk pembayaran midtrans, parse dari response
        $paymentType = $transaction->midtrans_payment_type;
        $response = $transaction->midtrans_response;

        if (!$paymentType) {
            return ucfirst($transaction->payment_method);
        }

        // QRIS
        if ($paymentType === 'qris') {
            return 'QRIS';
        }

        // BANK TRANSFER
        if ($paymentType === 'bank_transfer') {
            // Cek Permata VA
            if (isset($response['permata_va_number'])) {
                return 'Transfer Bank - Permata';
            }
            
            // Cek VA Numbers
            $bank = data_get($response, 'va_numbers.0.bank');
            if ($bank) {
                $bankNames = [
                    'bca' => 'BCA',
                    'bni' => 'BNI',
                    'bri' => 'BRI',
                    'mandiri' => 'Mandiri',
                    'bsi' => 'BSI',
                    'cimb' => 'CIMB Niaga',
                ];
                $bankName = $bankNames[strtolower($bank)] ?? strtoupper($bank);
                return "Transfer Bank - {$bankName}";
            }
            
            return 'Transfer Bank';
        }

        // ECHANNEL (Mandiri Bill Payment)
        if ($paymentType === 'echannel') {
            return 'Transfer Bank - Mandiri';
        }

        // E-WALLET
        if ($paymentType === 'gopay') {
            return 'E-Wallet - GoPay';
        }

        if ($paymentType === 'shopeepay') {
            return 'E-Wallet - ShopeePay';
        }

        // CREDIT CARD
        if ($paymentType === 'credit_card') {
            return 'Kartu Kredit';
        }

        // CONVENIENCE STORE
        if ($paymentType === 'cstore') {
            $store = data_get($response, 'store');
            if ($store) {
                return ucfirst($store);
            }
            return 'Convenience Store';
        }

        // PAYLATER
        if ($paymentType === 'akulaku') {
            return 'Paylater - Akulaku';
        }

        if ($paymentType === 'kredivo') {
            return 'Paylater - Kredivo';
        }

        // DEFAULT
        return 'Pembayaran Non Tunai';
    }
}