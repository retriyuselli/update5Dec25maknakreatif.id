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
        Schema::table('expenses', function (Blueprint $table) {
            // Increase no_nd column length from VARCHAR(255) or smaller to TEXT to avoid truncation
            $table->text('no_nd')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Revert back to original length (assuming it was VARCHAR(255))
            $table->string('no_nd', 255)->nullable()->change();
        });
    }
};
