<?php

namespace App\Filament\Resources\WorkflowActions\Pages;

use App\Filament\Resources\WorkflowActions\WorkflowActionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowActions extends ListRecords
{
    protected static string $resource = WorkflowActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
