<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCurrentStore
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            // 1) kalau belum ada current_store_id, set otomatis store pertama user
            if (! session()->has('current_store_id')) {
                $firstStoreId = $user->stores()->value('stores.id');
                if ($firstStoreId) {
                    session(['current_store_id' => (int) $firstStoreId]);
                }
            }

            // 2) kalau ada current_store_id tapi user bukan member store itu, reset
            $currentStoreId = session('current_store_id');
            if ($currentStoreId) {
                $isMember = $user->stores()->where('stores.id', $currentStoreId)->exists();

                if (! $isMember) {
                    session()->forget('current_store_id');

                    $firstStoreId = $user->stores()->value('stores.id');
                    if ($firstStoreId) {
                        session(['current_store_id' => (int) $firstStoreId]);
                    }
                }
            }
        }

        return $next($request);
    }
}
