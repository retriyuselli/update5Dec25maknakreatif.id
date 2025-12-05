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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('number')->unique();
            $table->string('no_kontrak')->nullable();
            $table->string('doc_kontrak')->nullable();
            $table->integer('pax');
            $table->text('note')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('promo', 15, 2)->default(0);
            $table->decimal('penambahan', 15, 2)->default(0);
            $table->decimal('pengurangan', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->nullable();
            $table->json('bukti')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->date('closing_date')->nullable();
            $table->string('status');
            $table->string('kategori_transaksi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
