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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); //
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null'); //
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null'); //
            $table->text('note')->nullable(); //
            $table->date('date_expense'); //
            $table->decimal('amount', 15, 2); //
            $table->text('no_nd')->nullable(); //
            $table->string('image')->nullable(); //
            $table->enum('kategori_transaksi', ['uang_masuk', 'uang_keluar'])->default('uang_keluar');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
