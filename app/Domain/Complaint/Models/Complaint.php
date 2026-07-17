<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Organization\Models\Opd;
use App\Domain\Organization\Models\Unit;
use App\Domain\Region\Models\District;
use App\Domain\Region\Models\Village;
use App\Domain\Shared\Models\UuidModel;
use App\Domain\Workflow\Models\Workflow;
use App\Domain\Workflow\Models\WorkflowHistory;
use App\Domain\Workflow\Models\WorkflowNode;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends UuidModel
{
    protected $fillable = [
        'ticket_number',
        'reporter_id',
        'category_id',
        'sub_category_id',
        'priority_id',
        'status_id',
        'workflow_id',
        'current_node_id',
        'current_opd_id',
        'current_unit_id',
        'current_role',
        'current_pic_id',
        'district_id',
        'village_id',
        'title',
        'description',
        'address',
        'latitude',
        'longitude',
        'reporter_name',
        'reporter_phone',
        'reporter_email',
        'is_anonymous',
        'sla_due_at',
        'responded_at',
        'resolved_at',
        'closed_at',
        'satisfaction_rating',
        'satisfaction_note',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_anonymous' => 'boolean',
            'sla_due_at' => 'datetime',
            'responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'satisfaction_rating' => 'integer',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(ComplaintSubCategory::class, 'sub_category_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ComplaintStatus::class, 'status_id');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function currentNode(): BelongsTo
    {
        return $this->belongsTo(WorkflowNode::class, 'current_node_id');
    }

    public function currentOpd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'current_opd_id');
    }

    public function currentUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'current_unit_id');
    }

    public function currentPic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_pic_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(ComplaintTimeline::class);
    }

    public function workflowHistories(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class);
    }
}
