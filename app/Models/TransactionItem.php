<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToStore;

class TransactionItem extends Model
{
    use BelongsToStore;
    protected $fillable = [
        'store_id',
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
        'price' => 'decimal:2',
        'qty' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
