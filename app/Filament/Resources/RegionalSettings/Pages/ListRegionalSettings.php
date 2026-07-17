<?php

namespace App\Filament\Resources\RegionalSettings\Pages;

use App\Filament\Resources\RegionalSettings\RegionalSettingResource;
use App\Domain\Configuration\Models\RegionalSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegionalSettings extends ListRecords
{
    protected static string $resource = RegionalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => RegionalSetting::query()->doesntExist()),
        ];
    }
}
