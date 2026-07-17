<?php

namespace App\Filament\Resources\Holidays\Schemas;

use App\Domain\Configuration\Models\Holiday;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HolidayInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('name'),
                IconEntry::make('is_national')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Holiday $record): bool => $record->trashed()),
            ]);
    }
}
