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
        // Add reconciliation fields to data_pembayarans
        Schema::table('data_pembayarans', function (Blueprint $table) {
            $table->enum('reconciliation_status', ['unmatched', 'matched', 'disputed', 'ignored'])
                ->default('unmatched')
                ->after('keterangan');
            $table->unsignedBigInteger('matched_bank_item_id')->nullable()->after('reconciliation_status');
            $table->decimal('match_confidence', 5, 2)->nullable()->after('matched_bank_item_id');
            $table->text('reconciliation_notes')->nullable()->after('match_confidence');
        });

        // Add reconciliation fields to pendapatan_lains
        Schema::table('pendapatan_lains', function (Blueprint $table) {
            $table->enum('reconciliation_status', ['unmatched', 'matched', 'disputed', 'ignored'])
                ->default('unmatched')
                ->after('keterangan');
            $table->unsignedBigInteger('matched_bank_item_id')->nullable()->after('reconciliation_status');
            $table->decimal('match_confidence', 5, 2)->nullable()->after('matched_bank_item_id');
            $table->text('reconciliation_notes')->nullable()->after('match_confidence');
        });

        // Add reconciliation fields to expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('reconciliation_status', ['unmatched', 'matched', 'disputed', 'ignored'])
                ->default('unmatched')
                ->after('amount');
            $table->unsignedBigInteger('matched_bank_item_id')->nullable()->after('reconciliation_status');
            $table->decimal('match_confidence', 5, 2)->nullable()->after('matched_bank_item_id');
            $table->text('reconciliation_notes')->nullable()->after('match_confidence');
        });

        // Add reconciliation fields to expense_ops
        Schema::table('expense_ops', function (Blueprint $table) {
            $table->enum('reconciliation_status', ['unmatched', 'matched', 'disputed', 'ignored'])
                ->default('unmatched')
                ->after('amount');
            $table->unsignedBigInteger('matched_bank_item_id')->nullable()->after('reconciliation_status');
            $table->decimal('match_confidence', 5, 2)->nullable()->after('matched_bank_item_id');
            $table->text('reconciliation_notes')->nullable()->after('match_confidence');
        });

        // Add reconciliation fields to pengeluaran_lains
        Schema::table('pengeluaran_lains', function (Blueprint $table) {
            $table->enum('reconciliation_status', ['unmatched', 'matched', 'disputed', 'ignored'])
                ->default('unmatched')
                ->after('amount');
            $table->unsignedBigInteger('matched_bank_item_id')->nullable()->after('reconciliation_status');
            $table->decimal('match_confidence', 5, 2)->nullable()->after('matched_bank_item_id');
            $table->text('reconciliation_notes')->nullable()->after('match_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove reconciliation fields from all transaction tables
        $tables = ['data_pembayarans', 'pendapatan_lains', 'expenses', 'expense_ops', 'pengeluaran_lains'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn([
                        'reconciliation_status',
                        'matched_bank_item_id',
                        'match_confidence',
                        'reconciliation_notes',
                    ]);
                });
            }
        }
    }
};
