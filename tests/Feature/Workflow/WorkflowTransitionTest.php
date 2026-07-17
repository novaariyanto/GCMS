<?php

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Application\Complaint\Services\ComplaintService;
use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\Priority;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

it('creates a complaint and transitions it through the seeded workflow', function (): void {
    $reporter = User::query()->where('email', 'masyarakat@gcms.local')->firstOrFail();
    $admin = User::query()->where('email', 'admin@gcms.local')->firstOrFail();
    $category = ComplaintCategory::query()->where('code', 'PELAYANAN')->firstOrFail();
    $priority = Priority::query()->where('code', 'MEDIUM')->firstOrFail();

    $complaint = app(ComplaintService::class)->create(CreateComplaintData::fromArray([
        'category_id' => $category->id,
        'priority_id' => $priority->id,
        'title' => 'Permintaan informasi publik',
        'description' => 'Pemohon membutuhkan informasi jadwal layanan terbaru.',
        'reporter_id' => $reporter->id,
        'reporter_name' => $reporter->name,
        'reporter_email' => $reporter->email,
    ]));

    $response = $this
        ->withHeader('Authorization', 'Bearer '.JWTAuth::fromUser($admin))
        ->postJson("/api/v1/complaints/{$complaint->id}/transition", [
            'action_code' => 'VERIFY',
            'remark' => 'Pengaduan valid dan dapat diteruskan.',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.status.code', ComplaintStatusCode::VERIFIED->value)
        ->assertJsonPath('data.current_node.code', 'VERIFY');

    $complaint->refresh()->load(['status', 'currentNode']);

    expect($complaint->status->code)->toBe(ComplaintStatusCode::VERIFIED->value)
        ->and($complaint->currentNode->code)->toBe('VERIFY')
        ->and($complaint->responded_at)->not->toBeNull();

    $this->assertDatabaseHas('workflow_histories', [
        'complaint_id' => $complaint->id,
        'from_user_id' => $admin->id,
        'action_code' => 'VERIFY',
        'remark' => 'Pengaduan valid dan dapat diteruskan.',
    ]);

    $this->assertDatabaseHas('complaint_timelines', [
        'complaint_id' => $complaint->id,
        'event_type' => 'workflow_transition',
        'title' => 'Verifikasi',
    ]);
});
