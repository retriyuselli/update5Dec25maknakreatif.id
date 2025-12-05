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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_batch_id')->constrained('journal_batches')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->date('transaction_date');
            $table->string('reference_number', 50)->nullable();
            $table->text('description');
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['account_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['journal_batch_id']);
            $table->index(['transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
