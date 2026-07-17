<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Shared\Models\UuidModel;

class ComplaintStatus extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'color',
        'is_final',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
