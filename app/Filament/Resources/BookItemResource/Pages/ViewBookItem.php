<?php

namespace App\Filament\Resources\BookItemResource\Pages;

use App\Filament\Resources\BookItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewBookItem extends ViewRecord
{
    protected static string $resource = BookItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print_qr')
                ->label('Print QR Code')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn() => route('book-items.print-qr', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Buku')
                    ->schema([
                        Infolists\Components\TextEntry::make('book.full_title')
                            ->label('Judul Buku')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('book.author')
                            ->label('Penulis'),
                        Infolists\Components\TextEntry::make('barcode')
                            ->label('Barcode')
                            ->copyable()
                            ->badge(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('QR Code')
                    ->schema([
                        Infolists\Components\ImageEntry::make('qr_code')
                            ->label('')
                            ->defaultImageUrl(fn($record) => $record->qr_code_url)
                            ->height(250),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('Status & Kondisi')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        Infolists\Components\TextEntry::make('condition')
                            ->label('Kondisi')
                            ->badge(),
                        Infolists\Components\TextEntry::make('current_location')
                            ->label('Lokasi Saat Ini')
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Informasi Akuisisi')
                    ->schema([
                        Infolists\Components\TextEntry::make('acquisition_date')
                            ->label('Tanggal Akuisisi')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('acquisition_price')
                            ->label('Harga')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(2),
            ]);
    }
}
