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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('name_event');
            $table->string('name_cpp');
            $table->string('name_cpw');
            $table->text('address')->nullable();
            $table->string('phone');
            $table->date('date_lamaran')->nullable();
            $table->date('date_akad')->nullable();
            $table->date('date_resepsi')->nullable();
            $table->string('venue')->nullable();
            $table->decimal('total_penawaran', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
