<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('vendor_price_histories', 'status')) {
            Schema::table('vendor_price_histories', function (Blueprint $table) {
                $table->enum('status', ['active', 'scheduled', 'archived'])
                    ->nullable()
                    ->after('effective_to');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendor_price_histories', 'status')) {
            Schema::table('vendor_price_histories', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};

