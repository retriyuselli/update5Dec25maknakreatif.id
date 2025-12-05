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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('period_month')->after('user_id')->default(date('n'));
            $table->integer('period_year')->after('period_month')->default(date('Y'));

            // Add composite index for better query performance
            $table->index(['period_year', 'period_month'], 'payrolls_period_index');

            // Add unique constraint to prevent duplicate payroll for same user in same period
            $table->unique(['user_id', 'period_year', 'period_month'], 'payrolls_user_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropIndex('payrolls_period_index');
            $table->dropUnique('payrolls_user_period_unique');
            $table->dropColumn(['period_month', 'period_year']);
        });
    }
};
