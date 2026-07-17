<?php

namespace App\Filament\Resources\ComplaintStatuses;

use App\Domain\Complaint\Models\ComplaintStatus;
use App\Filament\Resources\ComplaintStatuses\Pages\CreateComplaintStatus;
use App\Filament\Resources\ComplaintStatuses\Pages\EditComplaintStatus;
use App\Filament\Resources\ComplaintStatuses\Pages\ListComplaintStatuses;
use App\Filament\Resources\ComplaintStatuses\Pages\ViewComplaintStatus;
use App\Filament\Resources\ComplaintStatuses\Schemas\ComplaintStatusForm;
use App\Filament\Resources\ComplaintStatuses\Schemas\ComplaintStatusInfolist;
use App\Filament\Resources\ComplaintStatuses\Tables\ComplaintStatusesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ComplaintStatusResource extends Resource
{
    protected static ?string $model = ComplaintStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Status Pengaduan';

    protected static ?string $modelLabel = 'Status Pengaduan';

    protected static ?string $pluralModelLabel = 'Status Pengaduan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ComplaintStatusForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComplaintStatusInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplaintStatusesTable::configure($table);
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
            'index' => ListComplaintStatuses::route('/'),
            'create' => CreateComplaintStatus::route('/create'),
            'view' => ViewComplaintStatus::route('/{record}'),
            'edit' => EditComplaintStatus::route('/{record}/edit'),
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
