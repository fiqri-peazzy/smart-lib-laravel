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
                            ->label('QR Code Fisik')
                            ->content(function ($record) {
                                if (! $record || ! $record->barcode) {
                                    return new \Illuminate\Support\HtmlString('<span class="text-gray-400 text-sm">Belum ada QR Code</span>');
                                }
                                $qrcodeUrl = route('books.qrcode', $record);
                                $printUrl = route('books.qrcode.print', $record);

                                return new \Illuminate\Support\HtmlString('
                                    <div class="flex flex-col sm:flex-row items-start gap-4">
                                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200" style="width: 200px;">
                                            <div class="flex justify-center">
                                                <img src="'.$qrcodeUrl.'" alt="QR Code" style="width: 160px; height: 160px; image-rendering: pixelated;">
                                            </div>
                                            <div class="text-center mt-2 text-black font-mono font-bold tracking-wide" style="font-size: 10px;">
                                                '.$record->barcode.'
                                            </div>
                                            <div class="text-center text-gray-400 mt-1" style="font-size: 10px;">Label QR buku</div>
                                        </div>
                                        <div class="flex flex-col gap-2 mt-1">
                                            <button type="button" onclick="window.open(\''.$printUrl.'\', \'_blank\')" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors" style="background-color: #4f46e5;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                Print Label
                                            </button>
                                            <a href="'.$qrcodeUrl.'" download="qrcode-'.$record->barcode.'.png" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors" style="background-color: #16a34a;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
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

                        Forms\Components\TextInput::make('rack_location')
                            ->label('Lokasi Rak')
                            ->maxLength(50)
                            ->placeholder('Contoh: RAK-A-01')
                            ->helperText('Format: RAK-[A-Z]-[01-99]')
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

                        Forms\Components\TextInput::make('total_stock')
                            ->label('Total Stok')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->hidden(fn ($get) => $get('is_digital')),

                        Forms\Components\TextInput::make('available_stock')
                            ->label('Stok Tersedia')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->hidden(fn ($get) => $get('is_digital')),

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

                Tables\Columns\TextColumn::make('rack_location')
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
