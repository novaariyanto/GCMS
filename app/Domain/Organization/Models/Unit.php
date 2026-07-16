<?php

namespace App\Domain\Organization\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends UuidModel
{
    protected $fillable = [
        'opd_id',
        'division_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}
