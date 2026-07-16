<?php

namespace App\Filament\Resources\WorkflowActions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WorkflowActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('color')
                    ->required()
                    ->default('#0d6efd'),
                TextInput::make('icon'),
                Toggle::make('requires_remark')
                    ->required(),
                Toggle::make('requires_attachment')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
