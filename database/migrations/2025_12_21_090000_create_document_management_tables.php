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
        // 2. Document Categories (Jenis Surat)
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Surat Keputusan", "Memo"
            $table->string('code')->nullable(); // e.g., "SK", "MEMO"
            $table->enum('type', ['inbound', 'outbound', 'internal', 'policy'])->default('internal');
            $table->string('format_number')->nullable(); // e.g., "MEMO/{DEPT}/{ROMAN_MONTH}/{YEAR}"
            $table->foreignId('parent_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->boolean('is_approval_required')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Documents (Tabel Utama)
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('document_categories');
            $table->string('document_number')->unique()->nullable(); // Bisa null saat draft
            // $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete(); // Removed: using document_recipients pivot
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable(); // Rich Text
            $table->json('metadata')->nullable(); // Dynamic fields
            $table->date('date_effective')->nullable();
            $table->date('date_expired')->nullable();
            
            // Status Flow: draft -> pending (review) -> approved -> published -> archived
            $table->enum('status', ['draft', 'pending', 'approved', 'published', 'archived', 'rejected'])->default('draft');
            
            // Confidentiality Level
            $table->enum('confidentiality', ['public', 'internal', 'confidential', 'secret'])->default('internal');
            
            // Ownership
            // $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users'); // Uploader/Creator
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Document Approvals (Workflow)
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Approver
            $table->integer('step_order')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected', 'revised'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_path')->nullable(); // Digital Signature file
            $table->timestamps();
        });

        // 5. Document Recipients (Distribusi)
        Schema::create('document_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            
            // Bisa kirim ke User spesifik ATAU ke Department
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            // $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_cc')->default(false);
            $table->timestamps();
        });

        // 6. Document Attachments
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
        Schema::dropIfExists('document_recipients');
        Schema::dropIfExists('document_approvals');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
        // Schema::dropIfExists('departments');
    }
};
