<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('vendor_price_histories', 'status')) {
            DB::statement("ALTER TABLE vendor_price_histories MODIFY status ENUM('active','scheduled','archived') NULL DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendor_price_histories', 'status')) {
            DB::statement("ALTER TABLE vendor_price_histories MODIFY status ENUM('active','scheduled','archived') NULL DEFAULT NULL");
        }
    }
};
