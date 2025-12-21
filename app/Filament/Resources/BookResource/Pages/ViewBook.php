<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewBook extends ViewRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('add_item')
                ->label('Tambah Copy buku')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->url(fn() => route('filament.admin.resources.book-items.create', ['book' => $this->record->id]))
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Buku')
                    ->schema([
                        Infolists\Components\ImageEntry::make('cover_image')
                            ->label('Cover')
                            ->defaultImageUrl(fn($record) => $record->cover_url)
                            ->height(200),
                        Infolists\Components\TextEntry::make('full_title')
                            ->label('Judul Lengkap')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('author')
                            ->label('Penulis'),
                        Infolists\Components\TextEntry::make('isbn')
                            ->label('ISBN')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('publisher')
                            ->label('Penerbit'),
                        Infolists\Components\TextEntry::make('publication_year')
                            ->label('Tahun Terbit'),
                        Infolists\Components\TextEntry::make('edition')
                            ->label('Edisi'),
                        Infolists\Components\TextEntry::make('pages')
                            ->label('Jumlah Halaman')
                            ->suffix(' halaman'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Kategori & Lokasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('categories.name')
                            ->label('Kategori')
                            ->badge()
                            ->separator(','),
                        Infolists\Components\TextEntry::make('rack_location')
                            ->label('Lokasi Rak')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('recommendedForMajor.name')
                            ->label('Rekomendasi untuk')
                            ->badge(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Informasi Stok')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_stock')
                            ->label('Total Eksemplar')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('available_stock')
                            ->label('Tersedia')
                            ->badge()
                            ->color(fn($state) => $state > 0 ? 'success' : 'danger'),
                        Infolists\Components\IconEntry::make('is_available')
                            ->label('Status')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('Unggulan')
                            ->boolean(),
                    ])
                    ->columns(4),
            ]);
    }
}
