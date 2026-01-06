<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetupStoreController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();

        // kalau sudah punya store, langsung masuk admin
        if ($user->stores()->exists()) {
            return redirect('/admin');
        }

        return view('onboarding.setup-store');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // kalau sudah punya store, tidak perlu bikin lagi
        if ($user->stores()->exists()) {
            return redirect('/admin');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($user, $data, $request) {
            // 1) create store
            $store = Store::create([
                'name' => $data['name'],
                'owner_user_id' => $user->id,
            ]);

            // 2) attach ke pivot store_users sebagai owner
            $user->stores()->attach($store->id, [
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3) create subscription free
            $freePlan = Plan::where('code', 'free')->first();
            if (! $freePlan) {
                // fallback kalau belum ada seed plan
                $freePlan = Plan::create([
                    'code' => 'free',
                    'name' => 'Free',
                    'price' => 0,
                    'interval' => 'monthly',
                    'features' => [],
                ]);
            }

            Subscription::create([
                'store_id' => $store->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'started_at' => now(),
                'ends_at' => null,
            ]);

            // 4) set current store di session
            $request->session()->put('current_store_id', $store->id);
        });

        return redirect('/admin');
    }
}
