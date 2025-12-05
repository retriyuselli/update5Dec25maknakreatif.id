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
        if (! Schema::hasColumn('payrolls', 'pengurangan')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->decimal('pengurangan', 15, 2)->nullable()->after('tunjangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payrolls', 'pengurangan')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropColumn(['pengurangan']);
            });
        }
    }
};
