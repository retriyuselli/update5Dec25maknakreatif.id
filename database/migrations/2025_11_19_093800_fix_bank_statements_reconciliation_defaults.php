<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            if (! Schema::hasColumn('bank_statements', 'original_filename')) {
                $table->string('original_filename')->nullable()->after('file_path');
            }
            if (! Schema::hasColumn('bank_statements', 'total_records')) {
                $table->integer('total_records')->default(0)->after('reconciliation_original_filename');
            }
            if (! Schema::hasColumn('bank_statements', 'total_debit_reconciliation')) {
                $table->decimal('total_debit_reconciliation', 15, 2)->default(0)->after('total_records');
            }
            if (! Schema::hasColumn('bank_statements', 'total_credit_reconciliation')) {
                $table->decimal('total_credit_reconciliation', 15, 2)->default(0)->after('total_debit_reconciliation');
            }
            if (! Schema::hasColumn('bank_statements', 'reconciliation_status')) {
                $table->enum('reconciliation_status', ['uploaded', 'processing', 'completed', 'failed'])->default('uploaded')->after('total_credit_reconciliation');
            }
        });

        // Adjust defaults for existing columns without requiring doctrine/dbal
        if (Schema::hasColumn('bank_statements', 'total_records')) {
            DB::statement('ALTER TABLE bank_statements MODIFY total_records INT NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('bank_statements', 'total_debit_reconciliation')) {
            DB::statement('ALTER TABLE bank_statements MODIFY total_debit_reconciliation DECIMAL(15,2) NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('bank_statements', 'total_credit_reconciliation')) {
            DB::statement('ALTER TABLE bank_statements MODIFY total_credit_reconciliation DECIMAL(15,2) NOT NULL DEFAULT 0');
        }
        if (Schema::hasColumn('bank_statements', 'reconciliation_status')) {
            // Ensure enum default is 'uploaded'
            try {
                DB::statement("ALTER TABLE bank_statements MODIFY reconciliation_status ENUM('uploaded','processing','completed','failed') NOT NULL DEFAULT 'uploaded'");
            } catch (\Throwable $e) {
                // Fallback: if enum modification fails, make it nullable then set default via update
                DB::statement("UPDATE bank_statements SET reconciliation_status = IFNULL(reconciliation_status, 'uploaded')");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive changes; retain columns to avoid data loss
    }
};
