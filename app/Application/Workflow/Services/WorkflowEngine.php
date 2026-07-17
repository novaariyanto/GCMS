<?php

namespace App\Application\Workflow\Services;

use App\Application\Workflow\DTOs\TransitionComplaintData;
use App\Domain\Complaint\Enums\ComplaintStatusCode;
use App\Domain\Complaint\Events\ComplaintTransitioned;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Workflow\Models\WorkflowHistory;
use App\Domain\Workflow\Models\WorkflowNode;
use App\Domain\Workflow\Models\WorkflowTransition;
use App\Infrastructure\Persistence\Repositories\WorkflowRepository;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class WorkflowEngine
{
    /**
     * @var array<string, string>
     */
    private const ACTION_CAPABILITIES = [
        'FORWARD' => 'can_forward',
        'ASSIGN' => 'can_assign',
        'DELEGATE' => 'can_delegate',
        'ESCALATE' => 'can_escalate',
        'RETURN' => 'can_return',
        'REJECT' => 'can_reject',
        'APPROVE' => 'can_approve',
        'CLOSE' => 'can_close',
        'REQUEST_REVISION' => 'can_request_revision',
    ];

    public function __construct(private readonly WorkflowRepository $workflows) {}

    public function transition(TransitionComplaintData $data): Complaint
    {
        return DB::transaction(function () use ($data): Complaint {
            $complaint = Complaint::query()
                ->with(['currentNode', 'subCategory', 'status'])
                ->lockForUpdate()
                ->findOrFail($data->complaint_id);

            $transition = $this->resolveTransition($complaint, (string) $data->action_code);
            $action = $transition->action;
            $toNode = $transition->toNode;

            if ($action === null) {
                throw new RuntimeException('Workflow transition action is not configured.');
            }

            if (! $toNode instanceof WorkflowNode) {
                throw new RuntimeException('Workflow transition target node is not configured.');
            }

            if ($action->requires_remark && blank($data->remark)) {
                throw ValidationException::withMessages([
                    'remark' => 'Remark is required for this workflow action.',
                ]);
            }

            if ($action->requires_attachment && blank($data->attachment_path)) {
                throw ValidationException::withMessages([
                    'attachment_path' => 'Attachment is required for this workflow action.',
                ]);
            }

            $fromNode = $complaint->currentNode;
            $fromUserId = auth()->id();
            $toUser = $data->to_user_id !== null ? User::query()->find($data->to_user_id) : null;
            $startedAt = $this->nodeStartedAt($complaint);
            $completedAt = now();
            $currentPicId = $this->resolveCurrentPicId($complaint, $toNode, $toUser, $data->to_user_id);
            $currentOpdId = $toNode->opd_id
                ?? $toUser?->opd_id
                ?? $complaint->subCategory?->default_opd_id
                ?? ($toNode->id === $fromNode?->id ? $complaint->current_opd_id : null);
            $currentUnitId = $toNode->unit_id
                ?? $toUser?->unit_id
                ?? ($toNode->id === $fromNode?->id ? $complaint->current_unit_id : null);

            $update = [
                'current_node_id' => $toNode->id,
                'current_opd_id' => $currentOpdId,
                'current_unit_id' => $currentUnitId,
                'current_role' => $toNode->role_name,
                'current_pic_id' => $currentPicId,
            ];

            if ($transition->target_status_id !== null) {
                $update['status_id'] = $transition->target_status_id;
            }

            $statusCode = $transition->targetStatus?->code;

            if ($complaint->responded_at === null) {
                $update['responded_at'] = $completedAt;
            }

            if ($statusCode === ComplaintStatusCode::COMPLETED->value && $complaint->resolved_at === null) {
                $update['resolved_at'] = $completedAt;
            }

            if ($statusCode === ComplaintStatusCode::CLOSED->value && $complaint->closed_at === null) {
                $update['closed_at'] = $completedAt;
            }

            $complaint->update($update);

            $complaint->workflowHistories()->create([
                'from_user_id' => $fromUserId,
                'to_user_id' => $data->to_user_id,
                'from_role' => $fromNode?->role_name,
                'to_role' => $toNode->role_name,
                'from_node_id' => $fromNode?->id,
                'to_node_id' => $toNode->id,
                'action_id' => $action->id,
                'action_code' => $action->code,
                'remark' => $data->remark,
                'attachment_path' => $data->attachment_path,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'processing_seconds' => $startedAt->diffInSeconds($completedAt),
            ]);

            $complaint->timelines()->create([
                'user_id' => $fromUserId,
                'event_type' => 'workflow_transition',
                'title' => $action->label,
                'description' => $data->remark,
                'meta' => [
                    'action_code' => $action->code,
                    'from_node' => $fromNode?->code,
                    'to_node' => $toNode->code,
                    'status' => $statusCode,
                    'attachment_path' => $data->attachment_path,
                ],
                'occurred_at' => $completedAt,
            ]);

            DB::afterCommit(function () use ($complaint, $transition, $fromUserId): void {
                $transitionedComplaint = $complaint->fresh(['status', 'currentNode', 'currentOpd', 'currentPic']) ?? $complaint;

                event(new ComplaintTransitioned($transitionedComplaint, $transition, $fromUserId));
            });

            return $complaint->refresh();
        });
    }

    /**
     * @return Collection<int, WorkflowTransition>
     */
    public function availableActions(Complaint $complaint): Collection
    {
        if ($complaint->current_node_id === null) {
            return new Collection;
        }

        return $this->workflows
            ->getTransitionsFromNode($complaint->current_node_id)
            ->filter(fn (WorkflowTransition $transition): bool => $this->nodeAllowsAction($complaint->currentNode, $transition->action?->code))
            ->values();
    }

    private function resolveTransition(Complaint $complaint, string $actionCode): WorkflowTransition
    {
        $currentNode = $complaint->currentNode;

        if (! $currentNode instanceof WorkflowNode) {
            throw ValidationException::withMessages([
                'complaint_id' => 'Complaint does not have a current workflow node.',
            ]);
        }

        if (! $this->nodeAllowsAction($currentNode, $actionCode)) {
            throw ValidationException::withMessages([
                'action_code' => 'Action is not allowed from the current workflow node.',
            ]);
        }

        $transition = $this->workflows
            ->getTransitionsFromNode($currentNode->id)
            ->first(fn (WorkflowTransition $transition): bool => $transition->action?->code === $actionCode);

        if (! $transition instanceof WorkflowTransition) {
            throw ValidationException::withMessages([
                'action_code' => 'No active workflow transition is configured for this action.',
            ]);
        }

        return $transition;
    }

    private function nodeAllowsAction(?WorkflowNode $node, ?string $actionCode): bool
    {
        if (! $node instanceof WorkflowNode || $actionCode === null) {
            return false;
        }

        $capability = self::ACTION_CAPABILITIES[$actionCode] ?? null;

        return $capability === null || (bool) $node->{$capability};
    }

    private function nodeStartedAt(Complaint $complaint): CarbonInterface
    {
        $history = WorkflowHistory::query()
            ->where('complaint_id', $complaint->id)
            ->where('to_node_id', $complaint->current_node_id)
            ->latest('completed_at')
            ->first();

        return $history?->completed_at ?? $complaint->updated_at ?? $complaint->created_at ?? now();
    }

    private function resolveCurrentPicId(Complaint $complaint, WorkflowNode $toNode, ?User $toUser, ?string $toUserId): ?string
    {
        if ($toUser instanceof User) {
            return $toUser->id;
        }

        if ($toUserId !== null) {
            throw ValidationException::withMessages([
                'to_user_id' => 'Assigned user was not found.',
            ]);
        }

        if ($toNode->role_name === 'MASYARAKAT') {
            return $complaint->reporter_id;
        }

        if ($toNode->id === $complaint->current_node_id) {
            return $complaint->current_pic_id;
        }

        return null;
    }
}
