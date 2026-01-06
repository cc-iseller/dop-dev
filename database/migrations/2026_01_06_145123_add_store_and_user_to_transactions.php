<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('store_id')
                ->nullable()
                ->after('id')
                ->constrained('stores')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->after('store_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('paid_at')->nullable()->after('status');

            $table->index(['store_id', 'created_at'], 'transactions_store_created_index');
            $table->index(['store_id', 'status', 'created_at'], 'transactions_store_status_created_index');
            $table->index(['store_id', 'payment_method', 'created_at'], 'transactions_store_method_created_index');
            $table->index(['store_id', 'created_by', 'created_at'], 'transactions_store_user_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_store_created_index');
            $table->dropIndex('transactions_store_status_created_index');
            $table->dropIndex('transactions_store_method_created_index');
            $table->dropIndex('transactions_store_user_created_index');

            $table->dropColumn('paid_at');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('store_id');
        });
    }
};
