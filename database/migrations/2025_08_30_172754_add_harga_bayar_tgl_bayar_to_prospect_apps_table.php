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
            // Add financial fields for pricing and payment tracking
            $table->bigInteger('harga')
                ->nullable()
                ->after('service')
                ->comment('Estimated budget/price for the service package');

            $table->bigInteger('bayar')
                ->nullable()
                ->after('harga')
                ->comment('Amount paid by the prospect');

            $table->date('tgl_bayar')
                ->nullable()
                ->after('bayar')
                ->comment('Payment date if payment has been made');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_apps', function (Blueprint $table) {
            // Remove the financial fields
            $table->dropColumn(['harga', 'bayar', 'tgl_bayar']);
        });
    }
};
