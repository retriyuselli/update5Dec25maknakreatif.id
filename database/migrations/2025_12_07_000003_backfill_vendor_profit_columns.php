<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Backfill existing vendor rows with profit_amount and profit_margin based on harga_publish & harga_vendor
        if (Schema::hasTable('vendors')) {
            DB::table('vendors')->whereNull('profit_amount')->update(['profit_amount' => 0]);
            DB::table('vendors')->whereNull('profit_margin')->update(['profit_margin' => 0]);

            DB::statement(<<<SQL
                UPDATE vendors
                SET profit_amount = GREATEST(COALESCE(harga_publish,0) - COALESCE(harga_vendor,0), 0),
                    profit_margin = CASE WHEN COALESCE(harga_publish,0) > 0
                        THEN ROUND((GREATEST(COALESCE(harga_publish,0) - COALESCE(harga_vendor,0), 0) / COALESCE(harga_publish,0)) * 100, 2)
                        ELSE 0 END
            SQL);
        }
    }

    public function down(): void
    {
        // No-op: we won't revert data back
    }
};
