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
        Schema::table('product_penambahans', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('cascade')->after('product_id');
            $table->decimal('harga_publish', 15, 2)->default(0)->after('description');
            $table->decimal('harga_vendor', 15, 2)->default(0)->after('harga_publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_penambahans', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'harga_publish', 'harga_vendor']);
        });
    }
};
