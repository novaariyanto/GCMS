<?php

namespace App\Domain\Notification\Models;

use App\Domain\Shared\Models\UuidModel;

class NotificationMedia extends UuidModel
{
    protected $table = 'notification_media';

    protected $fillable = [
        'code',
        'name',
        'config',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
