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
        Schema::table('users', function (Blueprint $table) {
            // Document fields
            $table->string('contract_document')->nullable()->after('avatar_url');
            $table->string('identity_document')->nullable()->after('contract_document');
            $table->json('additional_documents')->nullable()->after('identity_document');

            // Notes fields
            $table->text('notes')->nullable()->after('additional_documents');
            $table->text('emergency_contact')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'contract_document',
                'identity_document',
                'additional_documents',
                'notes',
                'emergency_contact',
            ]);
        });
    }
};
