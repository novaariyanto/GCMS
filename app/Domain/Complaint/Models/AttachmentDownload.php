<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Shared\Traits\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttachmentDownload extends Model
{
    use HasUuid;

    protected $fillable = [
        'attachment_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(ComplaintAttachment::class, 'attachment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
