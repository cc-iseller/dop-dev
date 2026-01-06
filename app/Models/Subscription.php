<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'store_id', 'plan_id', 'status', 'started_at', 'ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') return false;

        if ($this->ends_at === null) return true;

        return $this->ends_at->isFuture();
    }

    public function canUseFeature(string $feature): bool
    {
        if (! $this->isActive()) return false;

        return (bool) optional($this->plan)->hasFeature($feature);
    }
}
