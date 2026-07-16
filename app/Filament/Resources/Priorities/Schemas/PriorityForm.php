<?php

namespace App\Filament\Resources\Priorities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PriorityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('color')
                    ->required()
                    ->default('#6c757d'),
                TextInput::make('level')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('sla_hours')
                    ->required()
                    ->numeric()
                    ->default(72),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
