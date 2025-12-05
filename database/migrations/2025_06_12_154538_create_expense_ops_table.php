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
        Schema::create('expense_ops', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //
            $table->decimal('amount', 15, 2); //
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null'); //
            $table->date('date_expense'); //
            $table->string('image')->nullable(); //
            $table->string('no_nd')->nullable(); //
            $table->text('note')->nullable(); //
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
        Schema::dropIfExists('expense_ops');
    }
};
