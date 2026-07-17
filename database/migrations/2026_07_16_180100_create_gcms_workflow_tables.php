<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('category_id')->nullable()->constrained('complaint_categories')->nullOnDelete();
            $table->foreignUuid('sub_category_id')->nullable()->constrained('complaint_sub_categories')->nullOnDelete();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workflow_nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->foreignUuid('opd_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->foreignUuid('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('role_name')->nullable();
            $table->integer('sequence')->default(1);
            $table->boolean('is_start')->default(false);
            $table->boolean('is_end')->default(false);
            $table->boolean('can_forward')->default(true);
            $table->boolean('can_assign')->default(true);
            $table->boolean('can_delegate')->default(false);
            $table->boolean('can_escalate')->default(false);
            $table->boolean('can_return')->default(true);
            $table->boolean('can_reject')->default(true);
            $table->boolean('can_approve')->default(false);
            $table->boolean('can_close')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_add_note')->default(true);
            $table->boolean('can_upload_attachment')->default(true);
            $table->boolean('can_change_sla')->default(false);
            $table->boolean('can_request_revision')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['workflow_id', 'code']);
            $table->index(['workflow_id', 'sequence']);
        });

        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('label');
            $table->string('color', 20)->default('#0d6efd');
            $table->string('icon')->nullable();
            $table->boolean('requires_remark')->default(false);
            $table->boolean('requires_attachment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->foreignUuid('from_node_id')->constrained('workflow_nodes')->cascadeOnDelete();
            $table->foreignUuid('to_node_id')->constrained('workflow_nodes')->cascadeOnDelete();
            $table->foreignUuid('action_id')->constrained('workflow_actions')->cascadeOnDelete();
            $table->foreignUuid('target_status_id')->nullable()->constrained('complaint_statuses')->nullOnDelete();
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['workflow_id', 'from_node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
        Schema::dropIfExists('workflow_actions');
        Schema::dropIfExists('workflow_nodes');
        Schema::dropIfExists('workflows');
    }
};
