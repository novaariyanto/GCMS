<?php

namespace App\Filament\Resources\RegionalSettings\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RegionalSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_name')
                    ->required(),
                TextInput::make('government_name')
                    ->required(),
                TextInput::make('logo'),
                TextInput::make('favicon'),
                ColorPicker::make('theme_color')
                    ->required()
                    ->default('#0B5ED7'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('website')
                    ->url(),
                TextInput::make('head_of_region'),
                TextInput::make('regional_secretary'),
                Textarea::make('dashboard_greeting')
                    ->columnSpanFull(),
                KeyValue::make('social_media')
                    ->keyLabel('Media')
                    ->valueLabel('URL')
                    ->columnSpanFull(),
            ]);
    }
}
