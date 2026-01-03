<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionItem;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_name',
        'payment_method',
        'status',
        'total_items',
        'total_amount',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_transaction_status',
        'midtrans_fraud_status',
        'midtrans_response',
    ];

    protected $casts = [
        'midtrans_response' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Helper untuk cek apakah transaksi sudah dibayar.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Helper untuk cek apakah transaksi masih pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Helper untuk cek apakah transaksi dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
