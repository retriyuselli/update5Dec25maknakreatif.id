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
        Schema::table('simulasi_produks', function (Blueprint $table) {
            $table->decimal('total_simulation', 15, 2)->default(0)->after('payment_simulation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulasi_produks', function (Blueprint $table) {
            $table->dropColumn('total_simulation');
        });
    }
};
