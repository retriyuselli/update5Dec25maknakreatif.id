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
            if (! Schema::hasColumn('bank_statements', 'reconciliation_original_filename')) {
                $table->string('reconciliation_original_filename')->nullable()->after('file_path');
            }
        });
    }

    /**
     * Reverse the migr
     */
    public function down(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropColumn(['reconciliation_original_filename']);
        });
    }
};
