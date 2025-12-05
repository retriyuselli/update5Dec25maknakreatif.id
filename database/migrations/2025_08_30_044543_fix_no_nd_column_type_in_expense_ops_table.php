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
        Schema::table('expense_ops', function (Blueprint $table) {
            // Change no_nd from int to string to match NotaDinas.no_nd format
            $table->string('no_nd')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_ops', function (Blueprint $table) {
            // Revert no_nd back to int
            $table->integer('no_nd')->nullable()->change();
        });
    }
};
