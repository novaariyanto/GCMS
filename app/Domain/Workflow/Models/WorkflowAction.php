<?php

namespace App\Domain\Workflow\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowAction extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'label',
        'color',
        'icon',
        'requires_remark',
        'requires_attachment',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'requires_remark' => 'boolean',
            'requires_attachment' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'action_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'action_id');
    }
}
