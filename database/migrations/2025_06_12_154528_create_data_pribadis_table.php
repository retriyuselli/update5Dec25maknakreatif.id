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
        Schema::create('data_pribadis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap'); //
            $table->string('email')->unique(); //
            $table->string('nomor_telepon')->nullable(); //
            $table->date('tanggal_lahir')->nullable(); //
            $table->date('tanggal_mulai_gabung')->nullable(); //
            $table->string('jenis_kelamin')->nullable(); //
            $table->text('alamat')->nullable(); //
            $table->string('foto')->nullable(); //
            $table->string('pekerjaan')->nullable(); //
            $table->decimal('gaji', 15, 2)->nullable(); //
            $table->text('motivasi_kerja')->nullable(); //
            $table->text('pelatihan')->nullable(); //
            $table->timestamps();
            $table->softDeletes(); //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pribadis');
    }
};
