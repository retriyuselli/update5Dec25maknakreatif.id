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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('kontrak')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('position')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_join')->nullable();
            $table->date('date_of_out')->nullable();
            $table->string('no_rek')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bank_name')->nullable();
            $table->string('photo')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
