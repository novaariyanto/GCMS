<?php

namespace App\Domain\Auth\Models;

use App\Domain\Shared\Models\UuidModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends UuidModel
{
    protected $fillable = [
        'user_id',
        'channel',
        'destination',
        'purpose',
        'code',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
