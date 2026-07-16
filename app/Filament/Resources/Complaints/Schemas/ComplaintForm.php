<?php

namespace App\Filament\Resources\Complaints\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_number')
                    ->required(),
                Select::make('reporter_id')
                    ->relationship('reporter', 'name'),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('sub_category_id')
                    ->relationship('subCategory', 'name'),
                Select::make('priority_id')
                    ->relationship('priority', 'name')
                    ->required(),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->required(),
                Select::make('workflow_id')
                    ->relationship('workflow', 'name'),
                Select::make('current_node_id')
                    ->relationship('currentNode', 'name'),
                Select::make('current_opd_id')
                    ->relationship('currentOpd', 'name'),
                Select::make('current_unit_id')
                    ->relationship('currentUnit', 'name'),
                TextInput::make('current_role'),
                Select::make('current_pic_id')
                    ->relationship('currentPic', 'name'),
                Select::make('district_id')
                    ->relationship('district', 'name'),
                Select::make('village_id')
                    ->relationship('village', 'name'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                TextInput::make('reporter_name')
                    ->required(),
                TextInput::make('reporter_phone')
                    ->tel(),
                TextInput::make('reporter_email')
                    ->email(),
                Toggle::make('is_anonymous')
                    ->required(),
                DateTimePicker::make('sla_due_at'),
                DateTimePicker::make('responded_at'),
                DateTimePicker::make('resolved_at'),
                DateTimePicker::make('closed_at'),
                TextInput::make('satisfaction_rating')
                    ->numeric(),
                Textarea::make('satisfaction_note')
                    ->columnSpanFull(),
            ]);
    }
}
