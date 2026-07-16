<?php

namespace App\Domain\Auth\Models;

use App\Domain\Shared\Traits\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
