<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => $this->record->status !== 'returned'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Peminjam'),
                        Infolists\Components\TextEntry::make('user.nim')
                            ->label('NIM'),
                        Infolists\Components\TextEntry::make('user.credit_score')
                            ->label('Credit Score')
                            ->badge()
                            ->color(fn($state) => $state >= 70 ? 'success' : 'danger'),
                        Infolists\Components\TextEntry::make('bookItem.book.title')
                            ->label('Judul Buku'),
                        Infolists\Components\TextEntry::make('bookItem.barcode')
                            ->label('Barcode')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Tanggal')
                    ->schema([
                        Infolists\Components\TextEntry::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->date('d M Y'),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('Jatuh Tempo')
                            ->date('d M Y')
                            ->badge()
                            ->color(fn($record) => $record->isOverdue() ? 'danger' : 'success'),
                        Infolists\Components\TextEntry::make('return_date')
                            ->label('Tanggal Kembali')
                            ->date('d M Y')
                            ->placeholder('Belum dikembalikan'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Status & Denda')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        Infolists\Components\IconEntry::make('is_extended')
                            ->label('Diperpanjang')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('fine_amount')
                            ->label('Denda')
                            ->money('IDR')
                            ->badge()
                            ->color(fn($state) => $state > 0 ? 'danger' : 'success'),
                        Infolists\Components\IconEntry::make('fine_paid')
                            ->label('Denda Dibayar')
                            ->boolean(),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Catatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan Peminjaman')
                            ->placeholder('Tidak ada catatan'),
                        Infolists\Components\TextEntry::make('return_notes')
                            ->label('Catatan Pengembalian')
                            ->placeholder('Belum dikembalikan'),
                        Infolists\Components\TextEntry::make('return_condition')
                            ->label('Kondisi Pengembalian')
                            ->badge(),
                    ])
                    ->columns(3)
                    ->visible(fn($record) => $record->return_date !== null),
            ]);
    }
}
