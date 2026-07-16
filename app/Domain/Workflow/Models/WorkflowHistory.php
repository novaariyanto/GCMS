<?php

namespace App\Domain\Workflow\Models;

use App\Domain\Complaint\Models\Complaint;
use App\Domain\Shared\Models\UuidModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowHistory extends UuidModel
{
    protected $fillable = [
        'complaint_id',
        'from_user_id',
        'to_user_id',
        'from_role',
        'to_role',
        'from_node_id',
        'to_node_id',
        'action_id',
        'action_code',
        'remark',
        'attachment_path',
        'started_at',
        'completed_at',
        'processing_seconds',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'processing_seconds' => 'integer',
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
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
}
