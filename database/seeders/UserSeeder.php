<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@example.com';

        // 1) Ambil admin kalau sudah ada, kalau belum ada baru create
        $admin = User::where('email', $email)->first();

        if (! $admin) {
            $admin = User::create([
                'name' => 'adminiseller',
                'email' => $email,
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]);
        } else {
            // 2) Pastikan atribut penting terset (tanpa reset password)
            $admin->forceFill([
                'name' => $admin->name ?: 'adminiseller',
                'is_admin' => true,
            ])->save();
        }

        // 3) Pastikan Default Store ada
        $storeId = DB::table('stores')->where('name', 'Default Store')->value('id');

        if (! $storeId) {
            $storeId = DB::table('stores')->insertGetId([
                'name' => 'Default Store',
                'owner_user_id' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Set owner hanya jika null
            DB::table('stores')
                ->where('id', $storeId)
                ->whereNull('owner_user_id')
                ->update([
                    'owner_user_id' => $admin->id,
                    'updated_at' => now(),
                ]);
        }

        // 4) Attach admin sebagai owner di store_users
        DB::table('store_users')->updateOrInsert(
            ['store_id' => $storeId, 'user_id' => $admin->id],
            ['role' => 'owner', 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
