<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();
            $table->string('customer_name')->nullable();

            $table->enum('payment_method', ['cash','debit','transfer','qris']);
            $table->enum('status', ['pending','paid','cancelled'])->default('pending');

            $table->integer('total_items');
            $table->decimal('total_amount', 15, 2);

            $table->timestamps();

            $table->index(['payment_method', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
