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
        // 1. Rename column bank_reconciliation_id to bank_statement_id in bank_reconciliation_items
        if (Schema::hasColumn('bank_reconciliation_items', 'bank_reconciliation_id')) {
            Schema::table('bank_reconciliation_items', function (Blueprint $table) {
                $table->renameColumn('bank_reconciliation_id', 'bank_statement_id');
            });
        }

        // 1.5. Clean up orphaned records that don't match bank_statements id
        // This prevents foreign key constraint violation
        if (Schema::hasTable('bank_reconciliation_items') && Schema::hasTable('bank_statements')) {
             \Illuminate\Support\Facades\DB::statement("DELETE FROM bank_reconciliation_items WHERE bank_statement_id NOT IN (SELECT id FROM bank_statements)");
        }

        // 2. Add foreign key constraint to bank_statements
        Schema::table('bank_reconciliation_items', function (Blueprint $table) {
            $table->foreign('bank_statement_id')
                  ->references('id')
                  ->on('bank_statements')
                  ->onDelete('cascade');
        });

        // 3. Drop deprecated table bank_reconciliations
        Schema::dropIfExists('bank_reconciliations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recreate bank_reconciliations table (basic structure)
        if (!Schema::hasTable('bank_reconciliations')) {
            Schema::create('bank_reconciliations', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->timestamps();
            });
        }

        // 2. Remove foreign key
        Schema::table('bank_reconciliation_items', function (Blueprint $table) {
            $table->dropForeign(['bank_statement_id']);
        });

        // 3. Rename column back
        if (Schema::hasColumn('bank_reconciliation_items', 'bank_statement_id')) {
            Schema::table('bank_reconciliation_items', function (Blueprint $table) {
                $table->renameColumn('bank_statement_id', 'bank_reconciliation_id');
            });
        }
    }
};
