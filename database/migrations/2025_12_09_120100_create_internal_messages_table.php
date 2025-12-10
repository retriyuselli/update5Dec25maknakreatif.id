<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('internal_messages')) {
            Schema::create('internal_messages', function (Blueprint $table) {
                $table->id();
                $table->string('subject')->nullable();
                $table->longText('message')->nullable();
                $table->string('type')->nullable();
                $table->string('priority')->default('normal');
                $table->string('status')->default('sent');
                $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('recipient_ids')->nullable();
                $table->json('cc_ids')->nullable();
                $table->json('bcc_ids')->nullable();
                $table->json('attachments')->nullable();
                $table->boolean('requires_response')->default(false);
                $table->dateTime('due_date')->nullable();
                $table->dateTime('read_at')->nullable();
                $table->dateTime('replied_at')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('thread_count')->default(0);
                $table->json('tags')->nullable();
                $table->string('department')->nullable();
                $table->boolean('is_public')->default(false);
                $table->boolean('is_pinned')->default(false);
                $table->json('read_by')->nullable();
                $table->json('deleted_by')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['sender_id']);
                $table->index(['parent_id']);

                $table->foreign('parent_id')->references('id')->on('internal_messages')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('internal_messages')) {
            Schema::dropIfExists('internal_messages');
        }
    }
};

