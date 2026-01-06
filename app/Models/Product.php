<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToStore;

class Product extends Model
{
    use BelongsToStore;
    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'description',
        'has_variants',
        'base_sku',
        'base_price',
        'base_stock',
        'is_active',
    ];

    protected $casts = [
        'has_variants' => 'boolean',
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
