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
            $table->decimal('grand_total', 15, 2)->default(0)->after('pengurangan');
            $table->decimal('payment_dp_amount', 15, 2)->default(0)->after('grand_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulasi_produks', function (Blueprint $table) {
            $table->dropColumn(['grand_total', 'payment_dp_amount']);
        });
    }
};
