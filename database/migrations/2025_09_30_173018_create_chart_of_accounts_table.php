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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20)->unique()->index();
            $table->string('account_name');
            $table->enum('account_type', [
                'HARTA',
                'KEWAJIBAN',
                'MODAL',
                'PENDAPATAN',
                'BEBAN_ATAS_PENDAPATAN',
                'BEBAN_OPERASIONAL',
                'PENDAPATAN_LAIN',
                'BEBAN_LAIN',
            ]);
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->integer('level')->default(1);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['account_type', 'is_active']);
            $table->index(['parent_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
