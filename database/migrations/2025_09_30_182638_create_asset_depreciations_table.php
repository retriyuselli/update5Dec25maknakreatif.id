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
        if (Schema::hasTable('asset_depreciations')) {
            return;
        }

        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained('fixed_assets')->cascadeOnDelete();
            $table->date('depreciation_date');
            $table->decimal('depreciation_amount', 15, 2);
            $table->decimal('accumulated_depreciation_before', 15, 2);
            $table->decimal('accumulated_depreciation_after', 15, 2);
            $table->decimal('book_value_before', 15, 2);
            $table->decimal('book_value_after', 15, 2);
            $table->unsignedBigInteger('journal_batch_id')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_adjustment')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['fixed_asset_id', 'depreciation_date']);
            $table->index(['depreciation_date']);
            $table->index(['is_adjustment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_depreciations');
    }
};
