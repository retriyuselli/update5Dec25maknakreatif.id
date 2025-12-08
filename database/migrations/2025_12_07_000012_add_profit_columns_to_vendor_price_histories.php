<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendor_price_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_price_histories', 'profit_amount')) {
                $table->decimal('profit_amount', 15, 2)->default(0)->after('harga_vendor');
            }
            if (! Schema::hasColumn('vendor_price_histories', 'profit_margin')) {
                $table->decimal('profit_margin', 10, 2)->default(0)->after('profit_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_price_histories', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_price_histories', 'profit_margin')) {
                $table->dropColumn('profit_margin');
            }
            if (Schema::hasColumn('vendor_price_histories', 'profit_amount')) {
                $table->dropColumn('profit_amount');
            }
        });
    }
};

