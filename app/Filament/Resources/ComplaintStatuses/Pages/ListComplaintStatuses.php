<?php

namespace App\Filament\Resources\ComplaintStatuses\Pages;

use App\Filament\Resources\ComplaintStatuses\ComplaintStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComplaintStatuses extends ListRecords
{
    protected static string $resource = ComplaintStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
