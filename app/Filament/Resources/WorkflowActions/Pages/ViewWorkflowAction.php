<?php

namespace App\Filament\Resources\WorkflowActions\Pages;

use App\Filament\Resources\WorkflowActions\WorkflowActionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkflowAction extends ViewRecord
{
    protected static string $resource = WorkflowActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
