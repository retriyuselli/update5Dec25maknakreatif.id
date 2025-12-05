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
        Schema::create('pendapatan_lains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->string('image')->nullable();
            $table->date('tgl_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('kategori_transaksi', ['uang_masuk', 'uang_keluar'])->default('uang_masuk');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendapatan_lains');
    }
};
