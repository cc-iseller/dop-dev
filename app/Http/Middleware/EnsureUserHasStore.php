<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasStore
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // kalau belum login, biarkan middleware auth yang urus
        if (! $user) {
            return $next($request);
        }

        // kalau belum punya store sama sekali => wajib setup store
        if ($user->stores()->count() === 0) {
            // hindari loop redirect
            if (! $request->routeIs('setup.store', 'setup.store.store')) {
                return redirect()->route('setup.store');
            }
        }

        return $next($request);
    }
}
