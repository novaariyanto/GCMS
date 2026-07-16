<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Shared\Models\UuidModel;

class Priority extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'color',
        'level',
        'sla_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'level' => 'integer',
            'sla_hours' => 'integer',
        ];
    }
}
