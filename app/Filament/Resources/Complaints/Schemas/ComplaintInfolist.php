<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Domain\Complaint\Models\Complaint;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan Tiket')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('ticket_number')
                            ->label('Ticket')
                            ->copyable(),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('priority.name')
                            ->label('Prioritas')
                            ->badge(),
                        TextEntry::make('category.name')
                            ->label('Kategori'),
                        TextEntry::make('subCategory.name')
                            ->label('Subkategori')
                            ->placeholder('-'),
                        TextEntry::make('currentPic.name')
                            ->label('PIC')
                            ->placeholder('Belum ditugaskan'),
                        TextEntry::make('sla_due_at')
                            ->label('SLA')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime(),
                        TextEntry::make('resolved_at')
                            ->label('Diselesaikan')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
                Section::make('Detail Pengaduan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('reporter_name')
                            ->label('Pelapor'),
                        TextEntry::make('reporter_phone')
                            ->label('Telepon Pelapor')
                            ->placeholder('-'),
                        TextEntry::make('reporter_email')
                            ->label('Email Pelapor')
                            ->placeholder('-'),
                        IconEntry::make('is_anonymous')
                            ->label('Anonim')
                            ->boolean(),
                    ]),
                Section::make('Timeline')
                    ->schema([
                        RepeatableEntry::make('timelines')
                            ->hiddenLabel()
                            ->state(fn (Complaint $record) => $record->timelines()->latest('occurred_at')->get())
                            ->schema([
                                TextEntry::make('occurred_at')
                                    ->label('Waktu')
                                    ->dateTime(),
                                TextEntry::make('title')
                                    ->label('Aktivitas')
                                    ->weight('bold'),
                                TextEntry::make('description')
                                    ->label('Catatan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                                TextEntry::make('user.name')
                                    ->label('Oleh')
                                    ->placeholder('Sistem'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Complaint $record): bool => $record->trashed()),
            ]);
    }
}
