<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('store_id')
                ->nullable()
                ->constrained('stores')
                ->nullOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('logged_in_at')->useCurrent();

            $table->timestamps();

            $table->index(['user_id', 'logged_in_at'], 'user_logins_user_time_index');
            $table->index(['store_id', 'logged_in_at'], 'user_logins_store_time_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
