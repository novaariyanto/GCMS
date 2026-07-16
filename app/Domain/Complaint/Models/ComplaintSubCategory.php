<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Organization\Models\Opd;
use App\Domain\Shared\Models\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintSubCategory extends UuidModel
{
    protected $fillable = [
        'category_id',
        'default_opd_id',
        'code',
        'name',
        'description',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function defaultOpd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'default_opd_id');
    }
}
