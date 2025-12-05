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
        Schema::table('data_pribadis', function (Blueprint $table) {
            // Add encrypted field for salary
            $table->text('gaji_encrypted')->nullable()->after('gaji')->comment('Encrypted salary data');

            // Add encrypted fields for other sensitive data
            $table->text('nomor_telepon_encrypted')->nullable()->after('nomor_telepon')->comment('Encrypted phone number');
            $table->text('alamat_encrypted')->nullable()->after('alamat')->comment('Encrypted address');

            // Add audit tracking
            $table->timestamp('last_salary_accessed_at')->nullable()->comment('Last time salary was accessed');
            $table->unsignedBigInteger('last_accessed_by')->nullable()->comment('User who last accessed this data');

            // Add foreign key for audit
            $table->foreign('last_accessed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_pribadis', function (Blueprint $table) {
            $table->dropForeign(['last_accessed_by']);
            $table->dropColumn([
                'gaji_encrypted',
                'nomor_telepon_encrypted',
                'alamat_encrypted',
                'last_salary_accessed_at',
                'last_accessed_by',
            ]);
        });
    }
};
