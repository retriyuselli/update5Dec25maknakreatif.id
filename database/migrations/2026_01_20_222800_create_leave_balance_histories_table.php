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
        Schema::create('leave_balance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_balance_id')->constrained()->cascadeOnDelete();
            $table->integer('amount'); // Positive for top up, negative for deduction (if needed later)
            $table->date('transaction_date');
            $table->string('reason');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balance_histories');
    }
};
