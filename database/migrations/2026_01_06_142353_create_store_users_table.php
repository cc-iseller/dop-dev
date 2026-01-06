<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                ->constrained('stores')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('role')->default('cashier'); // owner|admin|cashier
            $table->timestamps();

            $table->unique(['store_id', 'user_id']);
            $table->index(['user_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_users');
    }
};
