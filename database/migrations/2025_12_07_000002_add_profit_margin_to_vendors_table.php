<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'profit_margin')) {
                $table->decimal('profit_margin', 10, 2)->default(0)->after('profit_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'profit_margin')) {
                $table->dropColumn('profit_margin');
            }
        });
    }
};
