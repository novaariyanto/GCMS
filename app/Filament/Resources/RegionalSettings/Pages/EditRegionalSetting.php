<?php

namespace App\Filament\Resources\RegionalSettings\Pages;

use App\Filament\Resources\RegionalSettings\RegionalSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRegionalSetting extends EditRecord
{
    protected static string $resource = RegionalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
