<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Buku';

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('978-xxx-xxx-xxx-x'),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Buku')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subtitle')
                            ->label('Sub Judul')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('author')
                            ->label('Penulis')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('publisher')
                            ->label('Penerbit')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('publication_year')
                            ->label('Tahun Terbit')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y'))
                            ->placeholder(date('Y')),

                        Forms\Components\TextInput::make('edition')
                            ->label('Edisi')
                            ->maxLength(255)
                            ->placeholder('Contoh: 1st, 2nd, 3rd'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Fisik')
                    ->schema([
                        Forms\Components\TextInput::make('pages')
                            ->label('Jumlah Halaman')
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\Select::make('language')
                            ->label('Bahasa')
                            ->options([
                                'id' => 'Indonesia',
                                'en' => 'English',
                                'mixed' => 'Campuran',
                            ])
                            ->default('id')
                            ->required(),

                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Cover Buku')
                            ->image()
                            ->directory('book-covers')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kategori & Lokasi')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Kategori')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih satu atau lebih kategori')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('rack_location')
                            ->label('Lokasi Rak')
                            ->maxLength(50)
                            ->placeholder('Contoh: RAK-A-01')
                            ->helperText('Format: RAK-[A-Z]-[01-99]'),

                        Forms\Components\Select::make('recommended_for_major_id')
                            ->label('Rekomendasi untuk Prodi')
                            ->relationship('recommendedForMajor', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Buku ini direkomendasikan untuk prodi tertentu (optional)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Pengaturan')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Tersedia untuk Dipinjam')
                            ->default(true)
                            ->helperText('Nonaktifkan jika buku tidak boleh dipinjam'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Buku Unggulan')
                            ->default(false)
                            ->helperText('Tampilkan di koleksi unggulan homepage'),

                        Forms\Components\Placeholder::make('stock_info')
                            ->label('Informasi Stok')
                            ->content(
                                fn($record) => $record ?
                                    "Total: {$record->total_stock} | Available: {$record->available_stock}" :
                                    'Stok akan otomatis dihitung dari Book Items'
                            )
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('added_by')
                            ->default(Auth::id()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->defaultImageUrl(fn($record) => $record->cover_url)
                    ->size(60),

                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable()
                    ->toggleable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->separator(',')
                    ->limit(2)
                    ->searchable(),

                Tables\Columns\TextColumn::make('publication_year')
                    ->label('Tahun')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rack_location')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Total')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('available_stock')
                    ->label('Tersedia')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('recommended_for_major_id')
                    ->label('Rekomendasi Prodi')
                    ->relationship('recommendedForMajor', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Status Ketersediaan')
                    ->placeholder('Semua')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Buku Unggulan')
                    ->placeholder('Semua')
                    ->trueLabel('Unggulan')
                    ->falseLabel('Biasa'),

                Tables\Filters\Filter::make('available_stock')
                    ->label('Stok Habis')
                    ->query(fn($query) => $query->where('available_stock', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage_items')
                    ->label('Kelola Items')
                    ->icon('heroicon-o-squares-2x2')
                    ->color('info')
                    ->url(fn(Book $record): string => route('filament.admin.resources.book-items.index', [
                        'tableFilters' => [
                            'book_id' => ['value' => $record->id]
                        ]
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
