<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // =========================
        // A) DATA changes (boleh transaction)
        // =========================
        DB::transaction(function () {
            $adminId = DB::table('users')->orderBy('id')->value('id');

            if ($adminId) {
                DB::table('users')->where('id', $adminId)->update(['is_admin' => 1]);
            }

            $storeId = DB::table('stores')->where('name', 'Default Store')->value('id');

            if (! $storeId) {
                $storeId = DB::table('stores')->insertGetId([
                    'name' => 'Default Store',
                    'owner_user_id' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($adminId) {
                $exists = DB::table('store_users')
                    ->where('store_id', $storeId)
                    ->where('user_id', $adminId)
                    ->exists();

                if (! $exists) {
                    DB::table('store_users')->insert([
                        'store_id' => $storeId,
                        'user_id' => $adminId,
                        'role' => 'owner',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::table('categories')->whereNull('store_id')->update(['store_id' => $storeId]);
            DB::table('products')->whereNull('store_id')->update(['store_id' => $storeId]);
            DB::table('transactions')->whereNull('store_id')->update(['store_id' => $storeId]);

            if ($adminId) {
                DB::table('transactions')->whereNull('created_by')->update(['created_by' => $adminId]);
            }

            DB::table('transactions')
                ->where('status', 'paid')
                ->whereNull('paid_at')
                ->update(['paid_at' => now()]);

            if (Schema::hasColumn('transaction_items', 'store_id')) {
                DB::table('transaction_items')->whereNull('store_id')->update(['store_id' => $storeId]);
            }

            DB::statement("
                UPDATE product_variants pv
                JOIN products p ON p.id = pv.product_id
                SET pv.store_id = p.store_id
                WHERE pv.store_id IS NULL
            ");

            // seed plans
            $freePlanId = DB::table('plans')->where('code', 'free')->value('id');
            if (! $freePlanId) {
                $freePlanId = DB::table('plans')->insertGetId([
                    'code' => 'free',
                    'name' => 'Free',
                    'price' => 0,
                    'interval' => 'monthly',
                    'features' => json_encode([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $proPlanId = DB::table('plans')->where('code', 'pro')->value('id');
            if (! $proPlanId) {
                $proPlanId = DB::table('plans')->insertGetId([
                    'code' => 'pro',
                    'name' => 'Pro',
                    'price' => 0,
                    'interval' => 'monthly',
                    'features' => json_encode(['midtrans_payment', 'realtime_reports']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $subExists = DB::table('subscriptions')->where('store_id', $storeId)->exists();
            if (! $subExists) {
                DB::table('subscriptions')->insert([
                    'store_id' => $storeId,
                    'plan_id' => $freePlanId,
                    'status' => 'active',
                    'started_at' => now(),
                    'ends_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        // =========================
        // B) DDL changes (JANGAN dalam transaction)
        // =========================

        // 1) categories unique: drop old, add new
        // dari dump kamu: categories_name_unique
        DB::statement("ALTER TABLE categories DROP INDEX categories_name_unique");
        DB::statement("ALTER TABLE categories ADD UNIQUE categories_store_name_unique (store_id, name)");

        // 2) products unique base_sku: drop old, add new
        DB::statement("ALTER TABLE products DROP INDEX products_base_sku_unique");
        DB::statement("ALTER TABLE products ADD UNIQUE products_store_base_sku_unique (store_id, base_sku)");

        // 3) product_variants unique per store
        DB::statement("ALTER TABLE product_variants ADD UNIQUE product_variants_store_sku_unique (store_id, sku)");

        // 4) enforce NOT NULL setelah backfill selesai
        DB::statement("ALTER TABLE categories MODIFY store_id BIGINT(20) UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE products MODIFY store_id BIGINT(20) UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE transactions MODIFY store_id BIGINT(20) UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE product_variants MODIFY store_id BIGINT(20) UNSIGNED NOT NULL");
    }

    public function down(): void
    {
        // Migration data/DDL ini biasanya tidak dibalikkan.
    }
};
