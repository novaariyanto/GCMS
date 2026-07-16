<?php

namespace App\Domain\Workflow\Models;

use App\Domain\Complaint\Models\ComplaintStatus;
use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTransition extends UuidModel
{
    protected $fillable = [
        'workflow_id',
        'from_node_id',
        'to_node_id',
        'action_id',
        'target_status_id',
        'conditions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function fromNode(): BelongsTo
    {
        return $this->belongsTo(WorkflowNode::class, 'from_node_id');
    }

    public function toNode(): BelongsTo
    {
        return $this->belongsTo(WorkflowNode::class, 'to_node_id');
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(WorkflowAction::class, 'action_id');
    }

    public function targetStatus(): BelongsTo
    {
        return $this->belongsTo(ComplaintStatus::class, 'target_status_id');
    }
}
