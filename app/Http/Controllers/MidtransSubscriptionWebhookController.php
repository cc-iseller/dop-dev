<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransSubscriptionWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            Log::info('MIDTRANS SUBS WEBHOOK MASUK', $request->all());

            $orderId     = (string) $request->input('order_id');
            $statusCode  = (string) $request->input('status_code');
            $grossAmount = (string) $request->input('gross_amount');
            $signature   = (string) $request->input('signature_key');

            $serverKey = (string) config('services.midtrans.server_key');

            $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

            if (!hash_equals($expectedSignature, $signature)) {
                Log::error('MIDTRANS SUBS SIGNATURE INVALID', [
                    'order_id' => $orderId,
                    'expected' => $expectedSignature,
                    'incoming' => $signature,
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $payment = SubscriptionPayment::with('subscription')
                ->where('order_id', $orderId)
                ->first();

            if (!$payment) {
                Log::error('SUBS PAYMENT NOT FOUND', ['order_id' => $orderId]);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $trxStatus   = (string) $request->input('transaction_status');
            $fraudStatus = (string) ($request->input('fraud_status') ?? '');

            $isPaid = $trxStatus === 'settlement'
                || ($trxStatus === 'capture' && $fraudStatus === 'accept');

            DB::transaction(function () use ($payment, $request, $trxStatus, $isPaid) {
                // simpan payload webhook untuk audit/debug (aman karena payload json)
                $payload = (array) ($payment->payload ?? []);
                $payload['webhook'] = $request->all();

                // update status payment
                if ($isPaid) {
                    $payment->status = 'paid';
                } elseif (in_array($trxStatus, ['expire', 'cancel', 'deny'], true)) {
                    $payment->status = 'cancelled';
                } else {
                    // pending / authorize / dll
                    $payment->status = $payment->status ?: 'pending';
                }

                $payment->payload = $payload;
                $payment->save();

                // update subscription (pastikan kolom 'status' ada di tabel subscriptions)
                if ($payment->subscription) {
                    if ($isPaid) {
                        $payment->subscription->update(['status' => 'active']);
                    } elseif (in_array($trxStatus, ['expire', 'cancel', 'deny'], true)) {
                        $payment->subscription->update(['status' => 'inactive']);
                    }
                }
            });

            return response()->json(['status' => 'ok'], 200);

        } catch (\Throwable $e) {
            Log::error('MIDTRANS SUBS WEBHOOK 500', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['message' => 'server error'], 500);
        }
    }
}
