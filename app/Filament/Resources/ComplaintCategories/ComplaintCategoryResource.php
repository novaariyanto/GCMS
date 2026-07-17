<?php

namespace App\Filament\Resources\ComplaintCategories;

use App\Domain\Complaint\Models\ComplaintCategory;
use App\Filament\Resources\ComplaintCategories\RelationManagers\SubCategoriesRelationManager;
use App\Filament\Resources\ComplaintCategories\Pages\CreateComplaintCategory;
use App\Filament\Resources\ComplaintCategories\Pages\EditComplaintCategory;
use App\Filament\Resources\ComplaintCategories\Pages\ListComplaintCategories;
use App\Filament\Resources\ComplaintCategories\Pages\ViewComplaintCategory;
use App\Filament\Resources\ComplaintCategories\Schemas\ComplaintCategoryForm;
use App\Filament\Resources\ComplaintCategories\Schemas\ComplaintCategoryInfolist;
use App\Filament\Resources\ComplaintCategories\Tables\ComplaintCategoriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ComplaintCategoryResource extends Resource
{
    protected static ?string $model = ComplaintCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Kategori Pengaduan';

    protected static ?string $modelLabel = 'Kategori Pengaduan';

    protected static ?string $pluralModelLabel = 'Kategori Pengaduan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ComplaintCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComplaintCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplaintCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SubCategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComplaintCategories::route('/'),
            'create' => CreateComplaintCategory::route('/create'),
            'view' => ViewComplaintCategory::route('/{record}'),
            'edit' => EditComplaintCategory::route('/{record}/edit'),
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
