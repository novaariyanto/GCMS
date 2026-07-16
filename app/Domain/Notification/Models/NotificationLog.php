<?php

namespace App\Domain\Notification\Models;

use App\Domain\Shared\Models\UuidModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends UuidModel
{
    protected $fillable = [
        'user_id',
        'channel',
        'destination',
        'template',
        'payload',
        'status',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
