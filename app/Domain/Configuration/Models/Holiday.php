<?php

namespace App\Domain\Configuration\Models;

use App\Domain\Shared\Models\UuidModel;

class Holiday extends UuidModel
{
    protected $fillable = [
        'date',
        'name',
        'is_national',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_national' => 'boolean',
        ];
    }
}
