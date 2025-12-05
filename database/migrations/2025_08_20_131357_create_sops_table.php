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
        Schema::create('sops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained('sop_categories')->onDelete('cascade');
            $table->json('steps'); // Array of steps
            $table->json('supporting_documents')->nullable(); // Array of file paths
            $table->string('version', 10)->default('1.0');
            $table->boolean('is_active')->default(true);
            $table->date('effective_date')->nullable();
            $table->date('review_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('keywords')->nullable(); // For search functionality
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active']);
            $table->index(['category_id']);
            $table->index(['version']);
            $table->index(['effective_date']);
            $table->index(['review_date']);
            $table->fullText(['title', 'description', 'keywords']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sops');
    }
};
