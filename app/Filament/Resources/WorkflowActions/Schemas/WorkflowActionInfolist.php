<?php

namespace App\Filament\Resources\WorkflowActions\Schemas;

use App\Domain\Workflow\Models\WorkflowAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkflowActionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('label'),
                TextEntry::make('color'),
                TextEntry::make('icon')
                    ->placeholder('-'),
                IconEntry::make('requires_remark')
                    ->boolean(),
                IconEntry::make('requires_attachment')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (WorkflowAction $record): bool => $record->trashed()),
            ]);
    }
}
