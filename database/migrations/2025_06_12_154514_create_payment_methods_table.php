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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->string('cabang')->nullable();
            $table->string('no_rekening')->nullable();
            $table->boolean('is_cash')->default(false);
            $table->decimal('opening_balance', 15, 2)->default(0)->comment('Saldo awal rekening saat pertama kali dicatat di sistem.');
            $table->date('opening_balance_date')->nullable()->comment('Tanggal saldo awal dicatat.');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
