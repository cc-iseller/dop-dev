<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->string('provider')->default('midtrans');
            $table->string('order_id')->unique(); // untuk webhook lookup
            $table->unsignedBigInteger('amount')->default(0);

            $table->string('status')->default('pending'); // pending|paid|failed|expired|canceled
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at'], 'subscription_payments_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
