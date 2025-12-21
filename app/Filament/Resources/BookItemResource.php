<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookItemResource\Pages;
use App\Models\BookItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookItemResource extends Resource
{
    protected static ?string $model = BookItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Item Buku (Copy)';

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Buku')
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label('Pilih Buku')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $book = \App\Models\Book::find($state);
                                    $set('current_location', $book?->rack_location);
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('barcode')
                            ->label('Barcode')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->helperText('Barcode akan otomatis di-generate: BOOK-YYYY-XXXXX'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Kondisi')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Item')
                            ->options([
                                'available' => 'Tersedia',
                                'on_loan' => 'Sedang Dipinjam',
                                'maintenance' => 'Maintenance',
                                'lost' => 'Hilang',
                                'damaged' => 'Rusak',
                            ])
                            ->default('available')
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('condition')
                            ->label('Kondisi Fisik')
                            ->options([
                                'excellent' => 'Sangat Baik',
                                'good' => 'Baik',
                                'fair' => 'Cukup',
                                'poor' => 'Buruk',
                                'damaged' => 'Rusak',
                            ])
                            ->default('excellent')
                            ->required(),

                        Forms\Components\TextInput::make('current_location')
                            ->label('Lokasi Saat Ini')
                            ->maxLength(50)
                            ->placeholder('RAK-A-01'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Akuisisi')
                    ->schema([
                        Forms\Components\DatePicker::make('acquisition_date')
                            ->label('Tanggal Akuisisi')
                            ->default(now())
                            ->maxDate(now()),

                        Forms\Components\TextInput::make('acquisition_price')
                            ->label('Harga Akuisisi')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catatan kondisi atau riwayat item'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-o-qr-code'),

                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(40),

                Tables\Columns\TextColumn::make('book.author')
                    ->label('Penulis')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'available',
                        'warning' => 'on_loan',
                        'info' => 'maintenance',
                        'danger' => fn($state) => in_array($state, ['lost', 'damaged']),
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'on_loan' => 'Dipinjam',
                        'maintenance' => 'Maintenance',
                        'lost' => 'Hilang',
                        'damaged' => 'Rusak',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->colors([
                        'success' => 'excellent',
                        'info' => 'good',
                        'warning' => 'fair',
                        'danger' => fn($state) => in_array($state, ['poor', 'damaged']),
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'damaged' => 'Rusak',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_location')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('acquisition_date')
                    ->label('Tgl Akuisisi')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('book_id')
                    ->label('Buku')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'on_loan' => 'Dipinjam',
                        'maintenance' => 'Maintenance',
                        'lost' => 'Hilang',
                        'damaged' => 'Rusak',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options([
                        'excellent' => 'Sangat Baik',
                        'good' => 'Baik',
                        'fair' => 'Cukup',
                        'poor' => 'Buruk',
                        'damaged' => 'Rusak',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_qr')
                    ->label('Print QR')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(
                        fn(BookItem $record): string =>
                        route('book-items.print-qr', $record)
                    )
                    ->openUrlInNewTab(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('mark_damaged')
                        ->label('Tandai Rusak')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Alasan')
                                ->required()
                                ->placeholder('Jelaskan kerusakan...'),
                        ])
                        ->action(function (BookItem $record, array $data) {
                            $record->markAsDamaged($data['reason']);
                        }),

                    Tables\Actions\Action::make('mark_lost')
                        ->label('Tandai Hilang')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Alasan')
                                ->required()
                                ->placeholder('Jelaskan kehilangan...'),
                        ])
                        ->action(function (BookItem $record, array $data) {
                            $record->markAsLost($data['reason']);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_print_qr')
                        ->label('Print QR Codes')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(function ($records) {
                            // Implementasi bulk print nanti
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookItems::route('/'),
            'create' => Pages\CreateBookItem::route('/create'),
            'view' => Pages\ViewBookItem::route('/{record}'),
            'edit' => Pages\EditBookItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['book']);
    }
}
