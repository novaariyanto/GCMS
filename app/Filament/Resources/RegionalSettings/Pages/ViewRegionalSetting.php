<?php

namespace App\Filament\Resources\RegionalSettings\Pages;

use App\Filament\Resources\RegionalSettings\RegionalSettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRegionalSetting extends ViewRecord
{
    protected static string $resource = RegionalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
