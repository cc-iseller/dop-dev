<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
     protected $table = 'subscription_payments';
    protected $fillable = [
        'subscription_id', 'provider', 'order_id', 'amount', 'status', 'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
