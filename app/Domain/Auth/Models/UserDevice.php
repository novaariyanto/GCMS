<?php

namespace App\Domain\Auth\Models;

use App\Domain\Shared\Models\UuidModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends UuidModel
{
    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'fingerprint',
        'is_trusted',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_trusted' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
