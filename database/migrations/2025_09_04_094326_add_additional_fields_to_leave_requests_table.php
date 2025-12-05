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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('total_days');
            $table->string('emergency_contact')->nullable()->after('reason');
            $table->json('documents')->nullable()->after('emergency_contact');
            $table->unsignedBigInteger('replacement_employee_id')->nullable()->after('documents');
            $table->foreign('replacement_employee_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['replacement_employee_id']);
            $table->dropColumn(['reason', 'emergency_contact', 'documents', 'replacement_employee_id']);
        });
    }
};
