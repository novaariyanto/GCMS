<?php

namespace App\Filament\Resources\Workflows\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transitions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_node_id')
                    ->label('From node')
                    ->options(fn (): array => $this->getOwnerRecord()->nodes()->orderBy('sequence')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Select::make('to_node_id')
                    ->label('To node')
                    ->options(fn (): array => $this->getOwnerRecord()->nodes()->orderBy('sequence')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Select::make('action_id')
                    ->relationship('action', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('target_status_id')
                    ->label('Target status')
                    ->relationship('targetStatus', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                KeyValue::make('conditions')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('fromNode.name')
                    ->label('From')
                    ->searchable(),
                TextColumn::make('action.label')
                    ->label('Action')
                    ->badge()
                    ->searchable(),
                TextColumn::make('toNode.name')
                    ->label('To')
                    ->searchable(),
                TextColumn::make('targetStatus.name')
                    ->label('Target status')
                    ->placeholder('-')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
