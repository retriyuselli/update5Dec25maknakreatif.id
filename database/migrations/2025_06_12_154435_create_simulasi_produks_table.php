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
        Schema::create('simulasi_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->onDelete('cascade'); //
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); //
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); //
            $table->string('slug')->unique(); //
            $table->decimal('total_price', 15, 2)->default(0); //
            $table->decimal('promo', 15, 2)->default(0); //
            $table->decimal('penambahan', 15, 2)->default(0); //
            $table->decimal('pengurangan', 15, 2)->default(0); //
            $table->text('notes')->nullable(); //
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulasi_produks');
    }
};
