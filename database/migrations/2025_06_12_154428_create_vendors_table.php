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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('status', ['vendor', 'product'])->default('product');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('phone')->nullable();
            $table->string('pic_name')->nullable();
            $table->text('address')->nullable();
            $table->integer('stock')->default(0)->nullable();
            $table->text('description')->nullable();
            $table->decimal('harga_publish', 15, 2)->nullable();
            $table->decimal('harga_vendor', 15, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('kontrak_kerjasama')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
