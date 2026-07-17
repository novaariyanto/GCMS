<?php

namespace App\Domain\Region\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class);
    }
}
