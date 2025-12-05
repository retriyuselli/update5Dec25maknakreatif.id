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
        Schema::create('account_manager_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->year('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->decimal('target_amount', 15, 2)->default(1000000000.00);
            $table->decimal('achieved_amount', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, on_track, achieved, behind
            $table->unique(['user_id', 'year', 'month']); // Setiap AM hanya punya 1 target per bulan
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_manager_targets');
    }
};
