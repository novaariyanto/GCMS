<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Shared\Models\UuidModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintComment extends UuidModel
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'body',
        'is_internal',
        'mentions',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'mentions' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
