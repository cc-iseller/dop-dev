<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(Transaction $transaction): string
    {
        return Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $transaction->invoice_number,
                'gross_amount' => (int) $transaction->total_amount,
            ],
        ]);
    }
}
