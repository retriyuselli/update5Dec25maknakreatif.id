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
        if (!Schema::hasColumn('leave_balances', 'carried_over_days')) {
            Schema::table('leave_balances', function (Blueprint $table) {
                $table->integer('carried_over_days')->default(0)->after('allocated_days');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn('carried_over_days');
        });
    }
};
