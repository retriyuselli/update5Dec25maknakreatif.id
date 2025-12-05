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
        Schema::create('company_logos', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('website_url')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('category', ['client', 'partner', 'vendor', 'sponsor'])->default('client');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_email')->nullable();
            $table->enum('partnership_type', ['free', 'premium', 'enterprise'])->default('free');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'display_order']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_logos');
    }
};
