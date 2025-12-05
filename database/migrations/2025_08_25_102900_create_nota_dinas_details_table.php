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
        Schema::create('nota_dinas_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nota_dinas_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('keperluan');
            $table->string('event')->nullable();
            $table->decimal('jumlah_transfer', 18, 2);
            $table->string('invoice_number')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->string('invoice_file')->nullable();
            $table->enum('status_invoice', ['belum_dibayar', 'menunggu', 'sudah_dibayar'])->default('belum_dibayar');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('account_holder')->nullable();
            $table->enum('jenis_pengeluaran', ['operasional', 'wedding', 'lain-lain'])->default('wedding');
            $table->string('payment_stage')->default('down_payment');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_dinas_details');
    }
};
