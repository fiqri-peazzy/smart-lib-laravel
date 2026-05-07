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



                        Forms\Components\TextInput::make('title')
                            ->label('Judul Buku')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subtitle')
                            ->label('Sub Judul')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('author_id')
                            ->label('Penulis')
                            ->relationship('authorMaster', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('biography')
                                    ->rows(3),
                            ]),

                        Forms\Components\Select::make('publisher_id')
                            ->label('Penerbit')
                            ->relationship('publisherMaster', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email(),
                            ]),

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

                Forms\Components\Section::make('Konten Digital')
                    ->description('Pengaturan jika buku ini tersedia dalam format digital (E-Book/PDF)')
                    ->schema([
                        Forms\Components\Toggle::make('is_digital')
                            ->label('Tersedia Format Digital')
                            ->default(false)
                            ->reactive()
                            ->helperText('Jika aktif, user bisa langsung membaca file secara digital'),

                        Forms\Components\Group::make([
                            Forms\Components\FileUpload::make('digital_file_path')
                                ->label('File Digital (PDF)')
                                ->directory('digital-books')
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(51200) // 50MB
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('digital_file_size', $state?->getSize())),

                            Forms\Components\Select::make('digital_file_type')
                                ->label('Tipe Dokumen Digital')
                                ->options([
                                    'ebook' => 'E-Book',
                                    'skripsi' => 'Skripsi',
                                    'jurnal' => 'Jurnal',
                                    'modul' => 'Modul',
                                    'paper' => 'Paper/Karya Ilmiah',
                                ])
                                ->required(),

                            Forms\Components\Hidden::make('digital_file_size'),

                            Forms\Components\TextInput::make('keywords')
                                ->label('Kata Kunci')
                                ->placeholder('Pemisah koma (contoh: laravel, php, web)'),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('nim')
                                        ->label('NIM (Khusus Skripsi)')
                                        ->maxLength(20),
                                    Forms\Components\TextInput::make('supervisor')
                                        ->label('Dosen Pembimbing')
                                        ->maxLength(255),
                                ])
                                ->visible(fn ($get) => $get('digital_file_type') === 'skripsi'),
                        ])
                        ->visible(fn ($get) => $get('is_digital')),
                    ]),


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

                        Forms\Components\Select::make('rack_id')
                            ->label('Lokasi Rak')
                            ->relationship('rack', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->label('Kode Rak'),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Nama Rak'),
                            ])
                            ->hidden(fn ($get) => $get('is_digital')),

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
                            ->helperText('Nonaktifkan jika buku tidak boleh dipinjam')
                            ->hidden(fn ($get) => $get('is_digital')),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Buku Unggulan')
                            ->default(false)
                            ->helperText('Tampilkan di koleksi unggulan homepage'),



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
                    ->defaultImageUrl(asset('images/default-book-cover.png'))
                    ->size(60),

                Tables\Columns\TextColumn::make('is_digital')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Digital' : 'Fisik')
                    ->color(fn (bool $state) => $state ? 'info' : 'warning'),

                // Tables\Columns\TextColumn::make('barcode')
                //     ->label('Barcode')
                //     ->searchable()
                //     ->copyable()
                //     ->weight('bold')
                //     ->icon('heroicon-o-qr-code')
                //     ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50)
                    ->weight('bold'),

                // Tables\Columns\TextColumn::make('authorMaster.name')
                //     ->label('Penulis')
                //     ->searchable()
                //     ->sortable()
                //     ->placeholder('-'),

                // Tables\Columns\TextColumn::make('categories.name')
                //     ->label('Kategori')
                //     ->badge()
                //     ->separator(',')
                //     ->limit(2),

                // Tables\Columns\TextColumn::make('publication_year')
                //     ->label('Tahun terbit')
                //     ->sortable()
                //     ->toggleable(),

                Tables\Columns\TextColumn::make('rack.name')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Total Stok')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('available_stock')
                    ->label('Tersedia')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => ($state ?? 0) > 0 ? 'success' : 'danger')
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

                Tables\Filters\TernaryFilter::make('is_digital')
                    ->label('Jenis Koleksi')
                    ->placeholder('Semua')
                    ->trueLabel('Hanya Digital')
                    ->falseLabel('Hanya Fisik'),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Status Ketersediaan')
                    ->placeholder('Semua')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia')
                    ->queries(
                        true: fn ($query) => $query->where('is_available', true),
                        false: fn ($query) => $query->where('is_available', false),
                        blank: fn ($query) => $query,
                    ),

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
                Tables\Actions\DeleteAction::make(),
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
            \App\Filament\Resources\BookResource\RelationManagers\BookItemsRelationManager::class,
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
