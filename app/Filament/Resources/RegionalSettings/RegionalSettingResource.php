<?php

namespace App\Filament\Resources\RegionalSettings;

use App\Domain\Configuration\Models\RegionalSetting;
use App\Filament\Resources\RegionalSettings\Pages\CreateRegionalSetting;
use App\Filament\Resources\RegionalSettings\Pages\EditRegionalSetting;
use App\Filament\Resources\RegionalSettings\Pages\ListRegionalSettings;
use App\Filament\Resources\RegionalSettings\Pages\ViewRegionalSetting;
use App\Filament\Resources\RegionalSettings\Schemas\RegionalSettingForm;
use App\Filament\Resources\RegionalSettings\Schemas\RegionalSettingInfolist;
use App\Filament\Resources\RegionalSettings\Tables\RegionalSettingsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegionalSettingResource extends Resource
{
    protected static ?string $model = RegionalSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Konfigurasi';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Pengaturan Daerah';

    protected static ?string $modelLabel = 'Pengaturan Daerah';

    protected static ?string $pluralModelLabel = 'Pengaturan Daerah';

    protected static ?string $recordTitleAttribute = 'app_name';

    public static function form(Schema $schema): Schema
    {
        return RegionalSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RegionalSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegionalSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return RegionalSetting::query()->doesntExist();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegionalSettings::route('/'),
            'create' => CreateRegionalSetting::route('/create'),
            'view' => ViewRegionalSetting::route('/{record}'),
            'edit' => EditRegionalSetting::route('/{record}/edit'),
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
