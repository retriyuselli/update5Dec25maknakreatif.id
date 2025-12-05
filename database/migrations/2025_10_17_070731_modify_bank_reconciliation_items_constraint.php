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
        Schema::table('bank_reconciliation_items', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['bank_reconciliation_id']);

            // Remove the existing constraint and make the column nullable or flexible
            // We'll handle the relationship logic in the application level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_reconciliation_items', function (Blueprint $table) {
            // Restore original foreign key constraint
            $table->foreign('bank_reconciliation_id')
                ->references('id')
                ->on('bank_reconciliations')
                ->onDelete('cascade');
        });
    }
};
