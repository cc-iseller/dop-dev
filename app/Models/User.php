<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_users')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function storeUsers(): HasMany
    {
        return $this->hasMany(StoreUser::class);
    }

    public function ownedStores(): HasMany
    {
        return $this->hasMany(Store::class, 'owner_user_id');
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    // ===== Store aktif berbasis session =====

    public function currentStoreId(): ?int
    {
        $id = session('current_store_id');
        return $id ? (int) $id : null;
    }

    public function currentStore(): ?Store
    {
        $storeId = $this->currentStoreId();
        if (! $storeId) {
            return null;
        }

        return $this->stores()->where('stores.id', $storeId)->first();
    }

    public function hasFeature(string $feature): bool
    {
        $store = $this->currentStore();
        if (! $store) return false;

        $sub = $store->subscription()->with('plan')->first();
        if (! $sub || ! $sub->isActive()) return false;

        $features = $sub->plan?->features ?? [];
        return in_array($feature, $features, true);
    }
}
