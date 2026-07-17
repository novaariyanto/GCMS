<?php

namespace App\Filament\Resources\Opds;

use App\Domain\Organization\Models\Opd;
use App\Filament\Resources\Opds\Pages\CreateOpd;
use App\Filament\Resources\Opds\Pages\EditOpd;
use App\Filament\Resources\Opds\Pages\ListOpds;
use App\Filament\Resources\Opds\Pages\ViewOpd;
use App\Filament\Resources\Opds\Schemas\OpdForm;
use App\Filament\Resources\Opds\Schemas\OpdInfolist;
use App\Filament\Resources\Opds\Tables\OpdsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpdResource extends Resource
{
    protected static ?string $model = Opd::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'OPD';

    protected static ?string $modelLabel = 'OPD';

    protected static ?string $pluralModelLabel = 'OPD';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OpdForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OpdInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpdsTable::configure($table);
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
            'index' => ListOpds::route('/'),
            'create' => CreateOpd::route('/create'),
            'view' => ViewOpd::route('/{record}'),
            'edit' => EditOpd::route('/{record}/edit'),
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
