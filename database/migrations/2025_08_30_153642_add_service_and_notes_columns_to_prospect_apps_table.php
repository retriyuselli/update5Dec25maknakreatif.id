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
            // Drop existing service column if it exists
            if (Schema::hasColumn('prospect_apps', 'service')) {
                $table->dropColumn('service');
            }

            // Add new service column with correct enum values
            $table->enum('service', ['basic', 'standard', 'premium', 'enterprise'])
                ->nullable()
                ->after('user_size')
                ->comment('Service package selected by prospect');

            // Add notes column if it doesn't exist
            if (! Schema::hasColumn('prospect_apps', 'notes')) {
                $table->text('notes')
                    ->nullable()
                    ->after('reason_for_interest')
                    ->comment('Additional notes from prospect');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_apps', function (Blueprint $table) {
            // Remove the columns we added
            $table->dropColumn(['service', 'notes']);
        });
    }
};
