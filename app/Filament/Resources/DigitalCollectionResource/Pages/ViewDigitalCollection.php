<?php

namespace App\Filament\Resources\DigitalCollectionResource\Pages;

use App\Filament\Resources\DigitalCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class ViewDigitalCollection extends ViewRecord
{
    protected static string $resource = DigitalCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('download')
                ->label('Download File')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn() => Storage::url($this->record->file_path))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Dokumen')
                    ->schema([
                        Infolists\Components\ImageEntry::make('thumbnail')
                            ->label('Cover')
                            ->defaultImageUrl(fn($record) => $record->thumbnail_url)
                            ->height(200),
                        Infolists\Components\TextEntry::make('title')
                            ->label('Judul')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipe Dokumen')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'ebook' => 'E-Book',
                                'jurnal' => 'Jurnal',
                                'skripsi' => 'Skripsi',
                                'modul' => 'Modul',
                                'paper' => 'Paper',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('author')
                            ->label('Penulis'),
                        Infolists\Components\TextEntry::make('year')
                            ->label('Tahun'),
                        Infolists\Components\TextEntry::make('publisher')
                            ->label('Penerbit'),
                        Infolists\Components\TextEntry::make('isbn')
                            ->label('ISBN'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Informasi Akademik')
                    ->schema([
                        Infolists\Components\TextEntry::make('major.name')
                            ->label('Jurusan/Prodi')
                            ->badge(),
                        Infolists\Components\TextEntry::make('nim')
                            ->label('NIM Penulis'),
                        Infolists\Components\TextEntry::make('supervisor')
                            ->label('Pembimbing'),
                        Infolists\Components\TextEntry::make('keywords')
                            ->label('Keywords')
                            ->badge()
                            ->separator(','),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Informasi File')
                    ->schema([
                        Infolists\Components\TextEntry::make('file_type')
                            ->label('Format File')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                        Infolists\Components\TextEntry::make('file_size_readable')
                            ->label('Ukuran File'),
                        Infolists\Components\TextEntry::make('language')
                            ->label('Bahasa'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Statistik & Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('download_count')
                            ->label('Total Download')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success'),
                        Infolists\Components\TextEntry::make('view_count')
                            ->label('Total Views')
                            ->icon('heroicon-o-eye'),
                        Infolists\Components\IconEntry::make('is_public')
                            ->label('Akses Publik')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('Featured')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('uploader.name')
                            ->label('Diupload Oleh'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Upload')
                            ->dateTime('d M Y, H:i'),
                    ])
                    ->columns(3),
            ]);
    }
}
