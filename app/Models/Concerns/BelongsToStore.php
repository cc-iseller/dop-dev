<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToStore
{
    protected static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $builder) {
            $storeId = session('current_store_id');
            if ($storeId) {
                $builder->where($builder->getModel()->getTable() . '.store_id', $storeId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->store_id) && session()->has('current_store_id')) {
                $model->store_id = session('current_store_id');
            }
        });
    }
}
