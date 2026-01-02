<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;


class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_variant_id',
        'product_name_snapshot',
        'sku_snapshot',
        'variant_snapshot',
        'price',
        'qty',
        'subtotal',
    ];

    protected $casts = [
        'variant_snapshot' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}