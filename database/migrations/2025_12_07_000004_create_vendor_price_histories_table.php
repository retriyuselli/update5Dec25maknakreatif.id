<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->decimal('harga_publish', 15, 2)->default(0);
            $table->decimal('harga_vendor', 15, 2)->default(0);
            $table->decimal('profit_amount', 15, 2)->default(0);
            $table->decimal('profit_margin', 10, 2)->default(0);
            $table->dateTime('effective_from');
            $table->dateTime('effective_to')->nullable();
            $table->enum('status', ['active', 'scheduled', 'archived'])->default('active');
            $table->timestamps();

            $table->index(['vendor_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_price_histories');
    }
};

