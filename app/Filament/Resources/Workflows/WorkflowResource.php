<?php

namespace App\Filament\Resources\Workflows;

use App\Domain\Workflow\Models\Workflow;
use App\Filament\Resources\Workflows\Pages\CreateWorkflow;
use App\Filament\Resources\Workflows\Pages\EditWorkflow;
use App\Filament\Resources\Workflows\Pages\ListWorkflows;
use App\Filament\Resources\Workflows\Pages\ViewWorkflow;
use App\Filament\Resources\Workflows\RelationManagers\NodesRelationManager;
use App\Filament\Resources\Workflows\RelationManagers\TransitionsRelationManager;
use App\Filament\Resources\Workflows\Schemas\WorkflowForm;
use App\Filament\Resources\Workflows\Schemas\WorkflowInfolist;
use App\Filament\Resources\Workflows\Tables\WorkflowsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Workflow';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Workflow';

    protected static ?string $modelLabel = 'Workflow';

    protected static ?string $pluralModelLabel = 'Workflow';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WorkflowForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            NodesRelationManager::class,
            TransitionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflows::route('/'),
            'create' => CreateWorkflow::route('/create'),
            'view' => ViewWorkflow::route('/{record}'),
            'edit' => EditWorkflow::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
