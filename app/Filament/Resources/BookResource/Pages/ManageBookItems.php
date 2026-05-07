<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ManageBookItems extends ManageRelatedRecords
{
    protected static string $resource = BookResource::class;

    protected static string $relationship = 'bookItems';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    // Indonesian translations
    protected static ?string $title = 'Kelola Eksemplar Buku';

    protected static ?string $navigationLabel = 'Eksemplar';

    public function getTitle(): string
    {
        return 'Kelola Eksemplar: '.$this->getRecord()->title;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('qr_code')
                    ->label('QR Code')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('QR Code akan di-generate otomatis saat disimpan')
                    ->visible(fn ($record) => $record !== null),
                Forms\Components\Select::make('status')
                    ->options([
                        'available' => 'Tersedia',
                        'on_loan' => 'Dipinjam',
                        'maintenance' => 'Perbaikan',
                        'lost' => 'Hilang',
                        'damaged' => 'Rusak',
                    ])
                    ->default('available')
                    ->required(),
                Forms\Components\Select::make('condition')
                    ->options([
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'damaged' => 'Rusak',
                    ])
                    ->default('excellent')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('qr_code')
            ->heading('Daftar Eksemplar')
            ->modelLabel('Eksemplar')
            ->pluralModelLabel('Eksemplar')
            ->columns([
                Tables\Columns\TextColumn::make('qr_code')
                    ->label('QR Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'on_loan' => 'warning',
                        'maintenance' => 'info',
                        'lost', 'damaged' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent', 'good' => 'success',
                        'fair' => 'warning',
                        'poor', 'damaged' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Eksemplar'),
            ])
            ->actions([
                Tables\Actions\Action::make('print_qr')
                    ->label('Print QR')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('book-items.qrcode.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
