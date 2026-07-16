<?php

namespace App\Filament\Resources\WorkflowActions\Pages;

use App\Filament\Resources\WorkflowActions\WorkflowActionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowAction extends EditRecord
{
    protected static string $resource = WorkflowActionResource::class;

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
