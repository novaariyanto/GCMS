<?php

namespace App\Filament\Resources\Workflows\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WorkflowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                Select::make('sub_category_id')
                    ->relationship('subCategory', 'name'),
                Toggle::make('is_default')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
