<?php

namespace App\Domain\Workflow\Models;

use App\Domain\Organization\Models\Opd;
use App\Domain\Organization\Models\Unit;
use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowNode extends UuidModel
{
    protected $fillable = [
        'workflow_id',
        'code',
        'name',
        'opd_id',
        'unit_id',
        'role_name',
        'sequence',
        'is_start',
        'is_end',
        'can_forward',
        'can_assign',
        'can_delegate',
        'can_escalate',
        'can_return',
        'can_reject',
        'can_approve',
        'can_close',
        'can_edit',
        'can_add_note',
        'can_upload_attachment',
        'can_change_sla',
        'can_request_revision',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'is_start' => 'boolean',
            'is_end' => 'boolean',
            'can_forward' => 'boolean',
            'can_assign' => 'boolean',
            'can_delegate' => 'boolean',
            'can_escalate' => 'boolean',
            'can_return' => 'boolean',
            'can_reject' => 'boolean',
            'can_approve' => 'boolean',
            'can_close' => 'boolean',
            'can_edit' => 'boolean',
            'can_add_note' => 'boolean',
            'can_upload_attachment' => 'boolean',
            'can_change_sla' => 'boolean',
            'can_request_revision' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function outgoingTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_node_id');
    }

    public function incomingTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'to_node_id');
    }
}
