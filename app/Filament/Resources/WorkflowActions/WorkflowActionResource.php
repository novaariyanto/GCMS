<?php

namespace App\Filament\Resources\WorkflowActions;

use App\Domain\Workflow\Models\WorkflowAction;
use App\Filament\Resources\WorkflowActions\Pages\CreateWorkflowAction;
use App\Filament\Resources\WorkflowActions\Pages\EditWorkflowAction;
use App\Filament\Resources\WorkflowActions\Pages\ListWorkflowActions;
use App\Filament\Resources\WorkflowActions\Pages\ViewWorkflowAction;
use App\Filament\Resources\WorkflowActions\Schemas\WorkflowActionForm;
use App\Filament\Resources\WorkflowActions\Schemas\WorkflowActionInfolist;
use App\Filament\Resources\WorkflowActions\Tables\WorkflowActionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkflowActionResource extends Resource
{
    protected static ?string $model = WorkflowAction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Workflow';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Aksi Workflow';

    protected static ?string $modelLabel = 'Aksi Workflow';

    protected static ?string $pluralModelLabel = 'Aksi Workflow';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WorkflowActionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowActionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowActionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowActions::route('/'),
            'create' => CreateWorkflowAction::route('/create'),
            'view' => ViewWorkflowAction::route('/{record}'),
            'edit' => EditWorkflowAction::route('/{record}/edit'),
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
