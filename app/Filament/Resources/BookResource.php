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

    protected static ?string $modelLabel = 'Buku';

    protected static ?string $pluralModelLabel = 'Buku';

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

                        Forms\Components\Placeholder::make('barcode_display')
                            ->label('Barcode Fisik')
                            ->content(function ($record) {
                                if (! $record || ! $record->barcode) {
                                    return new \Illuminate\Support\HtmlString('<span class="text-gray-400 text-sm">Belum ada barcode</span>');
                                }
                                $barcodeUrl = route('books.barcode', $record);
                                $printUrl = route('books.barcode.print', $record);

                                return new \Illuminate\Support\HtmlString('
                                    <div class="flex items-start gap-4">
                                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200" style="width:240px">
                                            <div class="flex justify-center">
                                                <img src="'.$barcodeUrl.'" style="width:200px;height:64px;object-fit:fill;image-rendering:pixelated" alt="Barcode">
                                            </div>
                                            <div class="text-center mt-2 text-black font-mono font-bold tracking-widest" style="font-size:11px">'.$record->barcode.'</div>
                                            <div class="text-center text-gray-400 mt-1" style="font-size:11px">Label buku fisik</div>
                                        </div>
                                        <div class="flex flex-col gap-2 mt-1">
                                            <button type="button" onclick="window.open(\''.$printUrl.'\', \'_blank\')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#4f46e5">
                                                Print Label
                                            </button>
                                            <a href="'.$barcodeUrl.'" download="barcode-'.$record->barcode.'.png" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#16a34a">
                                                Unduh PNG
                                            </a>
                                        </div>
                                    </div>
                                ');
                            })
                            ->columnSpanFull(),

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

                        Forms\Components\TextInput::make('total_stock')
                            ->label('Total Stok')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Forms\Components\TextInput::make('available_stock')
                            ->label('Stok Tersedia')
                            ->numeric()
                            ->default(1)
                            ->required(),

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
                    ->defaultImageUrl(fn ($record) => $record->cover_url)
                    ->size(60),

                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-o-qr-code'),

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
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
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
                    ->query(fn ($query) => $query->where('available_stock', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
