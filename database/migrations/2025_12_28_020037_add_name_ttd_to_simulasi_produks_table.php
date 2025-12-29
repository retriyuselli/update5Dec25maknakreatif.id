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
        Schema::table('simulasi_produks', function (Blueprint $table) {
            $table->string('name_ttd')->nullable()->after('notes');
            $table->string('title_ttd')->nullable()->after('name_ttd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulasi_produks', function (Blueprint $table) {
            $table->dropColumn('name_ttd');
            $table->dropColumn('title_ttd');
        });
    }
};
