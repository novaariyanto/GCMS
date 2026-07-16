<?php

namespace App\Filament\Resources\ComplaintCategories\Pages;

use App\Filament\Resources\ComplaintCategories\ComplaintCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewComplaintCategory extends ViewRecord
{
    protected static string $resource = ComplaintCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
