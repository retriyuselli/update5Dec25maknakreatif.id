<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_piutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_id')->constrained('piutangs')->cascadeOnDelete();
            $table->string('nomor_pembayaran')->unique(); // PP/001/2025

            // Data Pembayaran
            $table->decimal('jumlah_pembayaran', 15, 2);
            $table->decimal('jumlah_bunga', 15, 2)->default(0);
            $table->decimal('denda', 15, 2)->default(0);
            $table->decimal('total_pembayaran', 15, 2);

            // Metode & Tanggal
            $table->foreignId('payment_method_id')->constrained();
            $table->date('tanggal_pembayaran');
            $table->date('tanggal_dicatat')->default(now());

            // Referensi & Konfirmasi
            $table->string('nomor_referensi')->nullable(); // Nomor referensi bank/transfer
            $table->foreignId('dibayar_oleh')->nullable()->constrained('users'); // Customer yang bayar
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users');

            // Bukti & Catatan
            $table->json('bukti_pembayaran')->nullable(); // File bukti
            $table->text('catatan')->nullable();

            // Status
            $table->enum('status', ['pending', 'dikonfirmasi', 'dibatalkan'])->default('pending');

            $table->timestamps();

            // Index untuk performa
            $table->index(['piutang_id', 'tanggal_pembayaran']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_piutangs');
    }
};
