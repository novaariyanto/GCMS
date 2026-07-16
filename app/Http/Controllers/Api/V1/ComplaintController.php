<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Application\Complaint\Services\ComplaintService;
use App\Application\Workflow\DTOs\TransitionComplaintData;
use App\Application\Workflow\Services\WorkflowEngine;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Models\Priority;
use App\Domain\Workflow\Models\WorkflowTransition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreComplaintRequest;
use App\Http\Requests\Api\V1\TransitionComplaintRequest;
use App\Http\Resources\Api\V1\ComplaintDetailResource;
use App\Http\Resources\Api\V1\ComplaintResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class ComplaintController extends Controller
{
    public function __construct(
        private readonly ComplaintService $complaints,
        private readonly WorkflowEngine $workflow,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/complaints",
     *     tags={"Complaints"},
     *     summary="Daftar pengaduan",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="Daftar pengaduan")
     * )
     */
    #[OA\Get(
        path: '/api/v1/complaints',
        summary: 'Daftar pengaduan',
        security: [['bearerAuth' => []]],
        tags: ['Complaints'],
        responses: [
            new OA\Response(response: 200, description: 'Daftar pengaduan'),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $complaints = Complaint::query()
            ->with(['category', 'subCategory', 'priority', 'status', 'currentOpd', 'currentPic'])
            ->when($request->query('q'), function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->query('status_id'), fn ($query, string $statusId) => $query->where('status_id', $statusId))
            ->when($request->query('category_id'), fn ($query, string $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return ComplaintResource::collection($complaints);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/complaints",
     *     tags={"Complaints"},
     *     summary="Buat pengaduan baru",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=201, description="Pengaduan berhasil dibuat")
     * )
     */
    #[OA\Post(
        path: '/api/v1/complaints',
        summary: 'Buat pengaduan baru',
        security: [['bearerAuth' => []]],
        tags: ['Complaints'],
        responses: [
            new OA\Response(response: 201, description: 'Pengaduan berhasil dibuat'),
        ]
    )]
    public function store(StoreComplaintRequest $request): ComplaintDetailResource
    {
        $data = $request->validated();
        $user = $request->user('api');
        $priority = $this->resolvePriority($data['priority_id'] ?? null);

        $complaint = $this->complaints->create(CreateComplaintData::fromArray([
            ...$data,
            'priority_id' => $priority->id,
            'reporter_id' => $user?->id,
            'reporter_name' => $data['reporter_name'] ?? $user?->name ?? 'Masyarakat',
            'reporter_phone' => $data['reporter_phone'] ?? $user?->phone,
            'reporter_email' => $data['reporter_email'] ?? $user?->email,
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
        ]));

        return new ComplaintDetailResource($complaint->load($this->detailRelations()));
    }

    public function show(Complaint $complaint): ComplaintDetailResource
    {
        return new ComplaintDetailResource($complaint->load($this->detailRelations()));
    }

    public function update(StoreComplaintRequest $request, Complaint $complaint): ComplaintDetailResource
    {
        $data = $request->validated();
        unset($data['priority_id']);

        $complaint->update($data);

        return new ComplaintDetailResource($complaint->refresh()->load($this->detailRelations()));
    }

    public function destroy(Complaint $complaint): JsonResponse
    {
        $complaint->delete();

        return response()->json([
            'message' => 'Pengaduan berhasil dihapus.',
        ]);
    }

    public function timeline(string $id): JsonResponse
    {
        $complaint = Complaint::query()->findOrFail($id);

        return response()->json([
            'data' => $complaint->timelines()
                ->with('user:id,name')
                ->orderByDesc('occurred_at')
                ->get(),
        ]);
    }

    public function histories(string $id): JsonResponse
    {
        $complaint = Complaint::query()->findOrFail($id);

        return response()->json([
            'data' => $complaint->workflowHistories()
                ->with(['fromUser:id,name', 'toUser:id,name', 'fromNode', 'toNode', 'action'])
                ->latest()
                ->get(),
        ]);
    }

    public function transition(TransitionComplaintRequest $request, string $id): ComplaintDetailResource
    {
        $data = $request->validated();
        $actionCode = $data['action_code'] ?? null;

        if ($actionCode === null && ! empty($data['transition_id'])) {
            $actionCode = WorkflowTransition::query()
                ->with('action')
                ->findOrFail($data['transition_id'])
                ->action?->code;
        }

        auth()->shouldUse('api');

        $complaint = $this->workflow->transition(TransitionComplaintData::fromArray([
            ...$data,
            'complaint_id' => $id,
            'action_code' => $actionCode,
        ]));

        return new ComplaintDetailResource($complaint->load($this->detailRelations()));
    }

    private function resolvePriority(?string $priorityId): Priority
    {
        $priority = $priorityId
            ? Priority::query()->find($priorityId)
            : Priority::query()->where('is_active', true)->orderBy('level')->first();

        if (! $priority) {
            throw ValidationException::withMessages([
                'priority_id' => ['Data prioritas belum tersedia.'],
            ]);
        }

        return $priority;
    }

    /**
     * @return list<string>
     */
    private function detailRelations(): array
    {
        return [
            'category',
            'subCategory',
            'priority',
            'status',
            'workflow',
            'currentNode',
            'currentOpd',
            'currentPic',
            'district',
            'village',
            'attachments',
        ];
    }
}
