<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sla extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'priority_id',
        'category_id',
        'response_hours',
        'resolution_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'response_hours' => 'integer',
            'resolution_hours' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }
}
