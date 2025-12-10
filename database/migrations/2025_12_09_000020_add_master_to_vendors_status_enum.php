<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('vendors')) {
            DB::statement("ALTER TABLE vendors MODIFY status ENUM('vendor','product','master') NOT NULL DEFAULT 'product'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendors')) {
            DB::statement("ALTER TABLE vendors MODIFY status ENUM('vendor','product') NOT NULL DEFAULT 'product'");
        }
    }
};
