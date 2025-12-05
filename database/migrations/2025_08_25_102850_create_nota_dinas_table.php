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
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('no_nd')->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('pengirim_id');
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->string('sifat')->nullable();
            $table->string('hal')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'dibayar', 'ditolak'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('pengirim_id')->references('id')->on('users');
            $table->foreign('penerima_id')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};
