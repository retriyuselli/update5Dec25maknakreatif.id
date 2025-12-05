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
        Schema::table('prospect_apps', function (Blueprint $table) {
            // Update service column if needed
            // This is a placeholder migration that was previously empty
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_apps', function (Blueprint $table) {
            // Reverse the changes if needed
        });
    }
};
