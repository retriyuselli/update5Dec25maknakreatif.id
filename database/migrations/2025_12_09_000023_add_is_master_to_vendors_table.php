<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'is_master')) {
                if (Schema::hasColumn('vendors', 'status_flags')) {
                    $table->boolean('is_master')->default(false)->after('status_flags');
                } else {
                    $table->boolean('is_master')->default(false)->after('status');
                }
            }
        });

        if (Schema::hasTable('vendors')) {
            if (Schema::hasColumn('vendors', 'status_flags')) {
                DB::update(
                    "UPDATE vendors SET is_master = 1 WHERE status = 'master' OR (status_flags IS NOT NULL AND JSON_CONTAINS(status_flags, ?))",
                    ['"master"']
                );
            } else {
                DB::update("UPDATE vendors SET is_master = 1 WHERE status = 'master'");
            }
        }
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'is_master')) {
                $table->dropColumn('is_master');
            }
        });
    }
};
