<?php

namespace App\Domain\Region\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends UuidModel
{
    protected $fillable = [
        'district_id',
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
