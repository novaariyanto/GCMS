<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Configuration\Models\AttachmentType;
use App\Domain\Shared\Models\UuidModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplaintAttachment extends UuidModel
{
    protected $fillable = [
        'complaint_id',
        'uploaded_by',
        'attachment_type_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'version',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'version' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function attachmentType(): BelongsTo
    {
        return $this->belongsTo(AttachmentType::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(AttachmentDownload::class, 'attachment_id');
    }
}
