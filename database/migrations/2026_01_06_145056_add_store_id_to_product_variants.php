<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('store_id')
                ->nullable()
                ->after('id')
                ->constrained('stores')
                ->cascadeOnDelete();

            $table->index(['store_id'], 'product_variants_store_id_index');
        });

        // Drop unique sku global -> nanti diganti unique per store (store_id, sku)
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('product_variants_sku_unique');
        });
    }

    public function down(): void
    {
        // Balikin unique sku global
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unique('sku', 'product_variants_sku_unique');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_store_id_index');
            $table->dropConstrainedForeignId('store_id');
        });
    }
};
