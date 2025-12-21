<?php

namespace App\Filament\Resources\FineResource\Pages;

use App\Filament\Resources\FineResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewFine extends ViewRecord
{
    protected static string $resource = FineResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Denda')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User'),
                        Infolists\Components\TextEntry::make('user.nim')
                            ->label('NIM'),
                        Infolists\Components\TextEntry::make('loan.bookItem.book.title')
                            ->label('Buku'),
                        Infolists\Components\TextEntry::make('days_overdue')
                            ->label('Hari Terlambat')
                            ->suffix(' hari')
                            ->badge()
                            ->color('danger'),
                        Infolists\Components\TextEntry::make('daily_rate')
                            ->label('Tarif per Hari')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('amount')
                            ->label('Total Denda')
                            ->money('IDR')
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Status Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        Infolists\Components\TextEntry::make('paid_amount')
                            ->label('Jumlah Dibayar')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('remaining_amount')
                            ->label('Sisa')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->badge(),
                        Infolists\Components\TextEntry::make('payment_reference')
                            ->label('Referensi'),
                        Infolists\Components\TextEntry::make('paid_at')
                            ->label('Tanggal Bayar')
                            ->date('d M Y H:i'),
                    ])
                    ->columns(3)
                    ->visible(fn($record) => $record->status === 'paid'),

                Infolists\Components\Section::make('Informasi Pembebasan')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_waived')
                            ->label('Dibebaskan')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('waivedBy.name')
                            ->label('Dibebaskan Oleh'),
                        Infolists\Components\TextEntry::make('waive_reason')
                            ->label('Alasan Pembebasan')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('waived_at')
                            ->label('Tanggal')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(3)
                    ->visible(fn($record) => $record->is_waived),
            ]);
    }
}
