<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\ProductVariant;

class Product extends Model
{
    protected $fillable = [
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
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
