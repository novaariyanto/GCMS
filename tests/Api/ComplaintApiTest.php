<?php

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Application\Complaint\Services\ComplaintService;
use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\ComplaintSubCategory;
use App\Domain\Complaint\Models\Priority;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

it('lists complaints for an authenticated API user', function (): void {
    $user = User::query()->where('email', 'masyarakat@gcms.local')->firstOrFail();
    $category = ComplaintCategory::query()->where('code', 'INFRASTRUKTUR')->firstOrFail();
    $priority = Priority::query()->where('code', 'LOW')->firstOrFail();

    app(ComplaintService::class)->create(CreateComplaintData::fromArray([
        'category_id' => $category->id,
        'priority_id' => $priority->id,
        'title' => 'Lampu jalan mati',
        'description' => 'Lampu jalan di depan balai desa sudah mati selama tiga hari.',
        'reporter_id' => $user->id,
        'reporter_name' => $user->name,
        'reporter_email' => $user->email,
    ]));

    $response = $this
        ->withHeader('Authorization', 'Bearer '.JWTAuth::fromUser($user))
        ->getJson('/api/v1/complaints');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Lampu jalan mati')
        ->assertJsonPath('data.0.category.code', 'INFRASTRUKTUR');
});

it('creates a complaint through the authenticated API', function (): void {
    $user = User::query()->where('email', 'masyarakat@gcms.local')->firstOrFail();
    $category = ComplaintCategory::query()->where('code', 'INFRASTRUKTUR')->firstOrFail();
    $subCategory = ComplaintSubCategory::query()->where('code', 'JALAN_RUSAK')->firstOrFail();
    $priority = Priority::query()->where('code', 'HIGH')->firstOrFail();

    $response = $this
        ->withHeader('Authorization', 'Bearer '.JWTAuth::fromUser($user))
        ->postJson('/api/v1/complaints', [
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'priority_id' => $priority->id,
            'title' => 'Jalan kabupaten berlubang',
            'description' => 'Lubang berada di dekat tikungan dan perlu segera diperbaiki.',
            'address' => 'Jl. Kabupaten KM 2',
            'reporter_phone' => '081234567891',
            'is_anonymous' => false,
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.title', 'Jalan kabupaten berlubang')
        ->assertJsonPath('data.category.code', 'INFRASTRUKTUR')
        ->assertJsonPath('data.sub_category.code', 'JALAN_RUSAK')
        ->assertJsonPath('data.priority.code', 'HIGH')
        ->assertJsonPath('data.status.code', 'NEW')
        ->assertJsonPath('data.current_node.code', 'START');

    $this->assertDatabaseHas('complaints', [
        'reporter_id' => $user->id,
        'title' => 'Jalan kabupaten berlubang',
        'reporter_email' => $user->email,
    ]);
});
