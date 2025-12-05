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
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->year('year')->default(now()->year)->after('leave_type_id');
            $table->index(['user_id', 'year', 'leave_type_id'], 'idx_leave_balances_user_year_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropIndex('idx_leave_balances_user_year_type');
            $table->dropColumn('year');
        });
    }
};
