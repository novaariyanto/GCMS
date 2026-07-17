<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplaintCategory extends UuidModel
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(ComplaintSubCategory::class, 'category_id');
    }
}
