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
        Schema::create('vendor_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->decimal('harga_publish', 15, 2)->nullable();
            $table->decimal('harga_vendor', 15, 2)->nullable();
            $table->decimal('profit_amount', 15, 2)->nullable();
            $table->decimal('profit_margin', 10, 2)->nullable();
            $table->dateTime('effective_from')->nullable();
            $table->dateTime('effective_to')->nullable();
            $table->string('status')->nullable();
            $table->string('kontrak')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_price_histories');
    }
};

