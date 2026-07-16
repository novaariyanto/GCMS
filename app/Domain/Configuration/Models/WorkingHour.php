<?php

namespace App\Domain\Configuration\Models;

use App\Domain\Shared\Models\UuidModel;

class WorkingHour extends UuidModel
{
    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
