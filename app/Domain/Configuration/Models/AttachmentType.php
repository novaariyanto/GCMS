<?php

namespace App\Domain\Configuration\Models;

use App\Domain\Shared\Models\UuidModel;

class AttachmentType extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'allowed_extensions',
        'max_size_kb',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allowed_extensions' => 'array',
            'max_size_kb' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
