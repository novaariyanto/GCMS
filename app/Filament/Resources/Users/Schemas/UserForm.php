<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('nik'),
                DateTimePicker::make('email_verified_at'),
                DateTimePicker::make('phone_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('avatar'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                Select::make('opd_id')
                    ->relationship('opd', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('last_login_at'),
                TextInput::make('last_login_ip'),
                KeyValue::make('preferences')
                    ->columnSpanFull(),
            ]);
    }
}
