<?php

namespace App\Filament\Resources\Opds\Pages;

use App\Filament\Resources\Opds\OpdResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOpd extends ViewRecord
{
    protected static string $resource = OpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
