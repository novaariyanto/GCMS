<?php

namespace App\Filament\Resources\RegionalSettings\Schemas;

use App\Domain\Configuration\Models\RegionalSetting;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RegionalSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('app_name'),
                TextEntry::make('government_name'),
                TextEntry::make('logo')
                    ->placeholder('-'),
                TextEntry::make('favicon')
                    ->placeholder('-'),
                TextEntry::make('theme_color'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('website')
                    ->placeholder('-'),
                TextEntry::make('head_of_region')
                    ->placeholder('-'),
                TextEntry::make('regional_secretary')
                    ->placeholder('-'),
                TextEntry::make('dashboard_greeting')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (RegionalSetting $record): bool => $record->trashed()),
            ]);
    }
}
