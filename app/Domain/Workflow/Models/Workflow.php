<?php

namespace App\Domain\Workflow\Models;

use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Models\ComplaintCategory;
use App\Domain\Complaint\Models\ComplaintSubCategory;
use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'sub_category_id',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(ComplaintSubCategory::class, 'sub_category_id');
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(WorkflowNode::class);
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }
}
