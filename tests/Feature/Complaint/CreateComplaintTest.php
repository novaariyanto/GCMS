<?php

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Application\Complaint\Services\ComplaintService;
use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\ComplaintSubCategory;
use App\Domain\Complaint\Models\Priority;
use App\Models\User;

it('creates a complaint through the application service with workflow bootstrap records', function (): void {
    $reporter = User::query()->where('email', 'masyarakat@gcms.local')->firstOrFail();
    $category = ComplaintCategory::query()->where('code', 'INFRASTRUKTUR')->firstOrFail();
    $subCategory = ComplaintSubCategory::query()->where('code', 'JALAN_RUSAK')->firstOrFail();
    $priority = Priority::query()->where('code', 'MEDIUM')->firstOrFail();

    $complaint = app(ComplaintService::class)->create(CreateComplaintData::fromArray([
        'category_id' => $category->id,
        'sub_category_id' => $subCategory->id,
        'priority_id' => $priority->id,
        'title' => 'Jalan rusak di depan pasar',
        'description' => 'Terdapat lubang besar yang membahayakan pengendara.',
        'address' => 'Jl. Merdeka No. 10',
        'reporter_id' => $reporter->id,
        'reporter_name' => $reporter->name,
        'reporter_phone' => '081234567890',
        'reporter_email' => $reporter->email,
        'is_anonymous' => false,
    ]));

    $complaint->load(['status', 'workflow', 'currentNode', 'timelines', 'workflowHistories']);

    expect($complaint->ticket_number)->toStartWith('GCMS-'.now()->format('Ymd').'-')
        ->and($complaint->status->code)->toBe(ComplaintStatusCode::NEW->value)
        ->and($complaint->workflow->code)->toBe('DEFAULT')
        ->and($complaint->currentNode->code)->toBe('START')
        ->and($complaint->current_pic_id)->toBe($reporter->id)
        ->and($complaint->sla_due_at)->not->toBeNull();

    $this->assertDatabaseHas('complaint_timelines', [
        'complaint_id' => $complaint->id,
        'event_type' => 'created',
        'title' => 'Pengaduan dibuat',
    ]);

    $this->assertDatabaseHas('workflow_histories', [
        'complaint_id' => $complaint->id,
        'action_code' => 'START',
        'to_node_id' => $complaint->current_node_id,
    ]);
});
