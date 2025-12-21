<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Major;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Kosongkan untuk staff/admin'),

                        Forms\Components\TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->revealable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText('Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('card_number')
                            ->label('Nomor Kartu')
                            ->maxLength(30)
                            ->helperText('Nomor kartu mahasiswa/dosen'),

                        Forms\Components\FileUpload::make('avatar')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('avatars')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Akademik')
                    ->schema([
                        Forms\Components\Select::make('major_id')
                            ->label('Jurusan/Prodi')
                            ->relationship('major', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->label('Kode')
                                    ->required()
                                    ->maxLength(10),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Prodi')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(3),
                            ]),

                        Forms\Components\TextInput::make('angkatan')
                            ->label('Tahun Angkatan')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(date('Y'))
                            ->placeholder(date('Y')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Sistem Perpustakaan')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required()
                            ->helperText('Pilih role untuk menentukan hak akses'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'suspended' => 'Suspended',
                                'graduated' => 'Lulus',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\TextInput::make('credit_score')
                            ->label('Credit Score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->suffix('/ 100')
                            ->helperText('Score kredibilitas peminjaman (0-100)'),

                        Forms\Components\TextInput::make('max_loans')
                            ->label('Maksimal Peminjaman')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->default(4)
                            ->suffix('buku')
                            ->helperText('Jumlah maksimal buku yang dapat dipinjam'),

                        Forms\Components\TextInput::make('total_fines')
                            ->label('Total Denda')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->helperText('Total denda yang belum dibayar'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn($record) => $record->avatar_url),

                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'staff',
                        'success' => 'dosen',
                        'primary' => 'mahasiswa',
                    ])
                    ->searchable(),

                Tables\Columns\TextColumn::make('major.code')
                    ->label('Prodi')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('angkatan')
                    ->label('Angkatan')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('credit_score')
                    ->label('Credit Score')
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->color(fn(string $state): string => match (true) {
                        $state >= 90 => 'success',
                        $state >= 70 => 'warning',
                        $state >= 50 => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match (true) {
                        $state >= 90 => 'heroicon-o-check-circle',
                        $state >= 70 => 'heroicon-o-exclamation-circle',
                        $state >= 50 => 'heroicon-o-x-circle',
                        default => 'heroicon-o-minus-circle',
                    }),

                Tables\Columns\TextColumn::make('max_loans')
                    ->label('Max Pinjam')
                    ->suffix(' buku')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_fines')
                    ->label('Denda')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn(string $state): string => $state > 0 ? 'danger' : 'success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'suspended',
                        'warning' => 'graduated',
                        'gray' => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Suspended',
                        'graduated' => 'Lulus',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('has_fines')
                    ->label('Punya Denda')
                    ->query(fn(Builder $query): Builder => $query->where('total_fines', '>', 0)),

                Tables\Filters\Filter::make('low_credit')
                    ->label('Credit Score Rendah')
                    ->query(fn(Builder $query): Builder => $query->where('credit_score', '<', 70)),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
