<?php

namespace App\Filament\Resources\WorkflowActions\Pages;

use App\Filament\Resources\WorkflowActions\WorkflowActionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowAction extends CreateRecord
{
    protected static string $resource = WorkflowActionResource::class;
}
