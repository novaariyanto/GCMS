<?php

namespace App\Application\Complaint\Services;

use App\Application\Complaint\DTOs\CreateComplaintData;
use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Events\ComplaintCreated;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Models\ComplaintStatus;
use App\Domain\Complaint\Models\ComplaintSubCategory;
use App\Domain\Complaint\Models\Priority;
use App\Domain\Workflow\Models\Workflow;
use App\Domain\Workflow\Models\WorkflowNode;
use App\Infrastructure\Persistence\Repositories\WorkflowRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ComplaintService
{
    public function __construct(private readonly WorkflowRepository $workflows) {}

    public function create(CreateComplaintData $data): Complaint
    {
        return DB::transaction(function () use ($data): Complaint {
            $workflow = $this->resolveWorkflow($data);
            $startNode = $this->resolveStartNode($workflow);
            $status = ComplaintStatus::query()
                ->where('code', ComplaintStatusCode::NEW->value)
                ->firstOrFail();
            $priority = Priority::query()->findOrFail($data->priority_id);
            $subCategory = $data->sub_category_id !== null
                ? ComplaintSubCategory::query()->find($data->sub_category_id)
                : null;
            $now = now();

            $complaint = Complaint::query()->create([
                'ticket_number' => $this->generateTicketNumber(),
                'reporter_id' => $data->reporter_id,
                'category_id' => $data->category_id,
                'sub_category_id' => $data->sub_category_id,
                'priority_id' => $data->priority_id,
                'status_id' => $status->id,
                'workflow_id' => $workflow->id,
                'current_node_id' => $startNode->id,
                'current_opd_id' => $startNode->opd_id ?? $subCategory?->default_opd_id,
                'current_unit_id' => $startNode->unit_id,
                'current_role' => $startNode->role_name,
                'current_pic_id' => $startNode->role_name === 'MASYARAKAT' ? $data->reporter_id : null,
                'district_id' => $data->district_id,
                'village_id' => $data->village_id,
                'title' => (string) $data->title,
                'description' => (string) $data->description,
                'address' => $data->address,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'reporter_name' => (string) $data->reporter_name,
                'reporter_phone' => $data->reporter_phone,
                'reporter_email' => $data->reporter_email,
                'is_anonymous' => $data->is_anonymous,
                'sla_due_at' => $now->copy()->addHours($priority->sla_hours),
            ]);

            $complaint->timelines()->create([
                'user_id' => $data->reporter_id,
                'event_type' => 'created',
                'title' => 'Pengaduan dibuat',
                'description' => 'Pengaduan berhasil dibuat dan menunggu proses verifikasi.',
                'meta' => [
                    'status' => ComplaintStatusCode::NEW->value,
                    'workflow_node' => $startNode->code,
                ],
                'occurred_at' => $now,
            ]);

            $complaint->workflowHistories()->create([
                'from_user_id' => null,
                'to_user_id' => $data->reporter_id,
                'from_role' => null,
                'to_role' => $startNode->role_name,
                'from_node_id' => null,
                'to_node_id' => $startNode->id,
                'action_id' => null,
                'action_code' => 'START',
                'remark' => 'Pengaduan dibuat',
                'started_at' => $now,
                'completed_at' => $now,
                'processing_seconds' => 0,
            ]);

            DB::afterCommit(function () use ($complaint): void {
                $createdComplaint = $complaint->fresh([
                    'category',
                    'subCategory',
                    'priority',
                    'status',
                    'workflow',
                    'currentNode',
                ]) ?? $complaint;

                event(new ComplaintCreated($createdComplaint));
            });

            return $complaint->refresh();
        });
    }

    private function generateTicketNumber(): string
    {
        $prefix = 'GCMS-'.now()->format('Ymd').'-';
        $latestTicket = Complaint::query()
            ->where('ticket_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $sequence = 1;

        if (is_string($latestTicket)) {
            $sequence = ((int) substr($latestTicket, -4)) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function resolveWorkflow(CreateComplaintData $data): Workflow
    {
        $workflow = $this->workflows->findForCategory($data->category_id, $data->sub_category_id)
            ?? $this->workflows->findDefault();

        if (! $workflow instanceof Workflow) {
            throw new RuntimeException('No active workflow is configured for complaints.');
        }

        return $workflow;
    }

    private function resolveStartNode(Workflow $workflow): WorkflowNode
    {
        $startNode = $workflow
            ->nodes()
            ->where('is_start', true)
            ->orderBy('sequence')
            ->first();

        if (! $startNode instanceof WorkflowNode) {
            throw new RuntimeException("Workflow [{$workflow->code}] does not have a start node.");
        }

        return $startNode;
    }
}
