<?php

namespace App\Domain\Organization\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opd extends UuidModel
{
    protected $table = 'opds';

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'description',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }
}
