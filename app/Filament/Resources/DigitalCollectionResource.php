<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalCollectionResource\Pages;
use App\Models\DigitalCollection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DigitalCollectionResource extends Resource
{
    protected static ?string $model = DigitalCollection::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Koleksi Digital';

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Dokumen')
                            ->options([
                                'ebook' => 'E-Book',
                                'jurnal' => 'Jurnal',
                                'skripsi' => 'Skripsi/Tugas Akhir',
                                'modul' => 'Modul Praktikum',
                                'paper' => 'Paper/Article',
                            ])
                            ->required()
                            ->reactive()
                            ->default('ebook'),

                        Forms\Components\TextInput::make('author')
                            ->label('Penulis/Author')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y'))
                            ->default(date('Y')),

                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(255)
                            ->placeholder('ISBN (optional)'),

                        Forms\Components\TextInput::make('publisher')
                            ->label('Penerbit')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Akademik')
                    ->schema([
                        Forms\Components\Select::make('major_id')
                            ->label('Jurusan/Prodi')
                            ->relationship('major', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Khusus untuk skripsi, paper, atau modul prodi tertentu'),

                        Forms\Components\TextInput::make('nim')
                            ->label('NIM Penulis')
                            ->maxLength(20)
                            ->visible(fn(Forms\Get $get) => $get('type') === 'skripsi')
                            ->helperText('NIM mahasiswa (untuk skripsi)'),

                        Forms\Components\TextInput::make('supervisor')
                            ->label('Pembimbing/Supervisor')
                            ->maxLength(255)
                            ->visible(fn(Forms\Get $get) => in_array($get('type'), ['skripsi', 'paper']))
                            ->helperText('Nama dosen pembimbing'),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('File & Media')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Upload File')
                            ->required()
                            ->directory('digital-collections')
                            ->acceptedFileTypes(['application/pdf', 'application/epub+zip', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(51200) // 50MB
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->helperText('Format: PDF, EPUB, DOCX. Maksimal 50MB'),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Cover/Thumbnail')
                            ->image()
                            ->directory('digital-collections/thumbnails')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('Cover atau thumbnail dokumen (optional)'),

                        Forms\Components\Hidden::make('file_type'),
                        Forms\Components\Hidden::make('file_size'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deskripsi & Metadata')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Deskripsi atau abstrak dokumen'),

                        Forms\Components\TagsInput::make('keywords')
                            ->label('Keywords/Tags')
                            ->separator(',')
                            ->placeholder('Tekan enter untuk menambah keyword')
                            ->helperText('Keywords untuk memudahkan pencarian')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('language')
                            ->label('Bahasa')
                            ->options([
                                'id' => 'Indonesia',
                                'en' => 'English',
                                'mixed' => 'Campuran',
                            ])
                            ->default('id')
                            ->required(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Pengaturan Akses')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Akses Publik')
                            ->default(true)
                            ->helperText('Jika aktif, semua user bisa akses. Jika tidak, hanya anggota prodi terkait.'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Tampilkan di Featured')
                            ->default(false)
                            ->helperText('Koleksi unggulan yang ditampilkan di homepage'),

                        Forms\Components\Hidden::make('uploaded_by')
                            ->default(Auth::id()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Cover')
                    ->defaultImageUrl(fn($record) => $record->thumbnail_url)
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->colors([
                        'primary' => 'ebook',
                        'success' => 'jurnal',
                        'warning' => 'skripsi',
                        'info' => 'modul',
                        'gray' => 'paper',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'ebook' => 'E-Book',
                        'jurnal' => 'Jurnal',
                        'skripsi' => 'Skripsi',
                        'modul' => 'Modul',
                        'paper' => 'Paper',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('major.code')
                    ->label('Prodi')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Format')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->color('gray'),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->sortable()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success'),

                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->icon('heroicon-o-eye')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Diupload Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Dokumen')
                    ->options([
                        'ebook' => 'E-Book',
                        'jurnal' => 'Jurnal',
                        'skripsi' => 'Skripsi',
                        'modul' => 'Modul',
                        'paper' => 'Paper',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('from')
                            ->label('Dari Tahun')
                            ->numeric(),
                        Forms\Components\TextInput::make('to')
                            ->label('Sampai Tahun')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->where('year', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn(Builder $query, $date): Builder => $query->where('year', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Akses Publik')
                    ->placeholder('Semua')
                    ->trueLabel('Publik')
                    ->falseLabel('Terbatas'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('Semua')
                    ->trueLabel('Featured')
                    ->falseLabel('Non-Featured'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(DigitalCollection $record): string => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListDigitalCollections::route('/'),
            'create' => Pages\CreateDigitalCollection::route('/create'),
            'view' => Pages\ViewDigitalCollection::route('/{record}'),
            'edit' => Pages\EditDigitalCollection::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
