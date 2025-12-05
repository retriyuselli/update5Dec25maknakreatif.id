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
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->string('asset_name');
            $table->enum('category', ['BUILDING', 'EQUIPMENT', 'FURNITURE', 'VEHICLE', 'COMPUTER', 'OTHER']);
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->enum('depreciation_method', ['STRAIGHT_LINE', 'DECLINING_BALANCE', 'UNITS_OF_PRODUCTION'])->default('STRAIGHT_LINE');
            $table->integer('useful_life_years')->default(0);
            $table->integer('useful_life_months')->default(0);
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->decimal('current_book_value', 15, 2);
            $table->string('location')->nullable();
            $table->enum('condition', ['EXCELLENT', 'GOOD', 'FAIR', 'POOR', 'DAMAGED'])->default('GOOD');
            $table->string('supplier')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts');
            $table->foreignId('depreciation_account_id')->constrained('chart_of_accounts');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['category', 'is_active']);
            $table->index(['asset_code']);
            $table->index(['purchase_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
