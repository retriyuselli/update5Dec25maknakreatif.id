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
        Schema::table('bank_statements', function (Blueprint $table) {
            // Make sure all fields that should be nullable are nullable
            $table->decimal('opening_balance', 15, 2)->nullable()->default(0)->change();
            $table->decimal('closing_balance', 15, 2)->nullable()->default(0)->change();
            $table->integer('no_of_debit')->nullable()->default(0)->change();
            $table->decimal('tot_debit', 15, 2)->nullable()->default(0)->change();
            $table->integer('no_of_credit')->nullable()->default(0)->change();
            $table->decimal('tot_credit', 15, 2)->nullable()->default(0)->change();
            $table->string('branch')->nullable()->change();
            $table->timestamp('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropColumn('processed_at');
        });
    }
};
