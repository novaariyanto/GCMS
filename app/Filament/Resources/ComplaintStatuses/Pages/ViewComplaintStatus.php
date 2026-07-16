<?php

namespace App\Filament\Resources\ComplaintStatuses\Pages;

use App\Filament\Resources\ComplaintStatuses\ComplaintStatusResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewComplaintStatus extends ViewRecord
{
    protected static string $resource = ComplaintStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
