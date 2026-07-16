<?php

namespace App\Filament\Resources\ComplaintStatuses\Pages;

use App\Filament\Resources\ComplaintStatuses\ComplaintStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditComplaintStatus extends EditRecord
{
    protected static string $resource = ComplaintStatusResource::class;

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
