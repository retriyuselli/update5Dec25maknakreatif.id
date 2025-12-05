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
        Schema::create('sop_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sop_id')->constrained('sops')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->json('steps');
            $table->json('supporting_documents')->nullable();
            $table->string('version', 10);
            $table->foreignId('revised_by')->constrained('users')->onDelete('cascade');
            $table->text('revision_notes')->nullable();
            $table->timestamp('revision_date');
            $table->timestamps();

            $table->index(['sop_id']);
            $table->index(['version']);
            $table->index(['revision_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sop_revisions');
    }
};
