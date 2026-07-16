<?php

namespace App\Domain\Configuration\Models;

use App\Domain\Shared\Models\UuidModel;

class RegionalSetting extends UuidModel
{
    protected $fillable = [
        'app_name',
        'government_name',
        'logo',
        'favicon',
        'theme_color',
        'address',
        'phone',
        'email',
        'website',
        'head_of_region',
        'regional_secretary',
        'dashboard_greeting',
        'social_media',
    ];

    protected function casts(): array
    {
        return [
            'social_media' => 'array',
        ];
    }
}
