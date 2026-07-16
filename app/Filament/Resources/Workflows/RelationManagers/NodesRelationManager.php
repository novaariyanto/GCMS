<?php

namespace App\Filament\Resources\Workflows\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NodesRelationManager extends RelationManager
{
    protected static string $relationship = 'nodes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(50),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sequence')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Select::make('opd_id')
                    ->label('OPD')
                    ->relationship('opd', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('role_name')
                    ->maxLength(255),
                Toggle::make('is_start')
                    ->label('Start node'),
                Toggle::make('is_end')
                    ->label('End node'),
                Toggle::make('can_forward')->default(true),
                Toggle::make('can_assign')->default(true),
                Toggle::make('can_delegate'),
                Toggle::make('can_escalate'),
                Toggle::make('can_return')->default(true),
                Toggle::make('can_reject')->default(true),
                Toggle::make('can_approve'),
                Toggle::make('can_close'),
                Toggle::make('can_edit'),
                Toggle::make('can_add_note')->default(true),
                Toggle::make('can_upload_attachment')->default(true),
                Toggle::make('can_change_sla'),
                Toggle::make('can_request_revision'),
                KeyValue::make('meta')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('sequence')
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('role_name')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('opd.name')
                    ->label('OPD')
                    ->placeholder('-')
                    ->searchable(),
                IconColumn::make('is_start')
                    ->boolean(),
                IconColumn::make('is_end')
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
