<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // free, pro
            $table->string('name');
            $table->unsignedBigInteger('price')->default(0); // rupiah
            $table->string('interval')->default('monthly'); // monthly|yearly
            $table->json('features')->nullable(); // ["midtrans_payment","realtime_reports"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
