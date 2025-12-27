<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VariantType;
use App\Models\ProductVariant;

class VariantOption extends Model
{
    protected $fillable = ['variant_type_id', 'value'];

    public function type()
    {
        return $this->belongsTo(VariantType::class, 'variant_type_id');
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class);
    }
}

