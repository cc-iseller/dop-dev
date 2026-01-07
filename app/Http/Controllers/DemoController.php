<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Store;
use App\Models\Plan;
use App\Models\Subscription;

class DemoController extends Controller
{
    public function start(Request $request)
    {
        abort_unless(config('demo.enabled'), 404);

        // 1) ambil / buat user demo
        $user = User::firstOrCreate(
            ['email' => config('demo.email')],
            [
                'name' => 'Demo User',
                'password' => Hash::make(config('demo.password')),
            ]
        );

        // 2) ambil / buat store demo
        $store = Store::firstOrCreate(
            ['name' => config('demo.store_name')],
            [
                // isi field wajib Store kamu di sini (mis. owner_id, slug, dll)
                // 'owner_id' => $user->id,
            ]
        );

        // 3) pastikan user terhubung ke store (sesuaikan relasi di project kamu)
        // contoh umum:
        // $user->stores()->syncWithoutDetaching([$store->id]);
        // atau kalau store punya owner_id:
        // $store->owner_id = $user->id; $store->save();

        // 4) paksa plan PRO aktif
        $proPlan = Plan::where('name', 'PRO')->orWhere('slug', 'pro')->first();
        if ($proPlan) {
            Subscription::updateOrCreate(
                ['store_id' => $store->id],
                [
                    'plan_id' => $proPlan->id,
                    'status' => 'active',
                    'expires_at' => now()->addYears(10),
                ]
            );
        }

        // 5) login + tandai session demo
        Auth::login($user);
        session([
            'is_demo' => true,
            'demo_store_id' => $store->id,
        ]);

        // arahkan ke dashboard utama kamu
        return redirect()->to('/admin'); // ganti ke route dashboard kamu
    }

    public function reset(Request $request)
    {
        abort_unless(config('demo.enabled'), 404);
        abort_unless(session('is_demo') === true, 403);

        // reset data demo: paling aman via seeder / truncate data tertentu
        // NOTE: sesuaikan table-table kasir kamu
        DB::transaction(function () {
            // contoh:
            // DB::table('sales')->truncate();
            // DB::table('products')->truncate();
            // DB::table('customers')->truncate();
        });

        return back()->with('status', 'Demo data berhasil di-reset.');
    }
}
