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
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}

