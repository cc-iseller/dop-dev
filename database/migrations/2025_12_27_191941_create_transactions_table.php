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
            $table->enum('payment_method', ['cash','debit','transfer','qris','midtrans'])->default('cash');
            $table->enum('status', ['pending','paid','cancelled'])->default('pending');
            $table->integer('total_items');
            $table->decimal('total_amount', 15, 2);
            $table->string('midtrans_transaction_id')->nullable()->comment('Transaction ID dari Midtrans');
            $table->string('midtrans_payment_type')->nullable()->comment('Tipe pembayaran Midtrans: gopay, bank_transfer, credit_card, etc.');
            $table->string('midtrans_transaction_status')->nullable()->comment('Status transaksi Midtrans: pending, settlement, expire, deny, cancel');
            $table->string('midtrans_fraud_status')->nullable()->comment('Fraud status Midtrans: accept, challenge');
            $table->json('midtrans_response')->nullable()->comment('Simpan response Midtrans untuk debugging');
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
