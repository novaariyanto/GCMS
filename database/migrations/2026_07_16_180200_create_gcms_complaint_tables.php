<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::create('complaints', function (Blueprint $table) use ($driver) {
            $table->uuid('id')->primary();
            $table->string('ticket_number', 40)->unique();
            $table->foreignUuid('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('category_id')->constrained('complaint_categories');
            $table->foreignUuid('sub_category_id')->nullable()->constrained('complaint_sub_categories')->nullOnDelete();
            $table->foreignUuid('priority_id')->constrained('priorities');
            $table->foreignUuid('status_id')->constrained('complaint_statuses');
            $table->foreignUuid('workflow_id')->nullable()->constrained('workflows')->nullOnDelete();
            $table->foreignUuid('current_node_id')->nullable()->constrained('workflow_nodes')->nullOnDelete();
            $table->foreignUuid('current_opd_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignUuid('current_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('current_role')->nullable();
            $table->foreignUuid('current_pic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignUuid('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('reporter_name');
            $table->string('reporter_phone', 30)->nullable();
            $table->string('reporter_email')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('sla_due_at')->nullable()->index();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedTinyInteger('satisfaction_rating')->nullable();
            $table->text('satisfaction_note')->nullable();
            if ($driver !== 'sqlite') {
                $table->fullText(['title', 'description', 'ticket_number']);
            } else {
                $table->index('ticket_number');
            }
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status_id', 'created_at']);
            $table->index(['current_opd_id', 'status_id']);
            $table->index(['current_pic_id', 'status_id']);
            $table->index(['category_id', 'created_at']);
        });

        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('attachment_type_id')->nullable()->constrained('attachment_types')->nullOnDelete();
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('complaint_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('body');
            $table->boolean('is_internal')->default(false);
            $table->json('mentions')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['complaint_id', 'is_internal']);
        });

        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_role')->nullable();
            $table->string('to_role')->nullable();
            $table->foreignUuid('from_node_id')->nullable()->constrained('workflow_nodes')->nullOnDelete();
            $table->foreignUuid('to_node_id')->nullable()->constrained('workflow_nodes')->nullOnDelete();
            $table->foreignUuid('action_id')->nullable()->constrained('workflow_actions')->nullOnDelete();
            $table->string('action_code', 50)->nullable();
            $table->text('remark')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('processing_seconds')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['complaint_id', 'created_at']);
        });

        Schema::create('complaint_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attachment_downloads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('attachment_id')->constrained('complaint_attachments')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachment_downloads');
        Schema::dropIfExists('complaint_timelines');
        Schema::dropIfExists('workflow_histories');
        Schema::dropIfExists('complaint_comments');
        Schema::dropIfExists('complaint_attachments');
        Schema::dropIfExists('complaints');
    }
};
