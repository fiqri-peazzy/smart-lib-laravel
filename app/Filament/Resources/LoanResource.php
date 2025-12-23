<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\BookItem;
use App\Models\Loan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Peminjaman';

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjam')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'nim', 'email'])
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        // Validate user can borrow
                                        if (!$user->canBorrow()) {
                                            $set('user_id', null);
                                            \Filament\Notifications\Notification::make()
                                                ->title('User tidak dapat meminjam')
                                                ->body('User memiliki denda > Rp 50.000 atau status tidak aktif')
                                                ->danger()
                                                ->send();
                                        }

                                        // Show user info
                                        $activeLoans = $user->activeLoans()->count();
                                        $set('user_info', "Credit Score: {$user->credit_score} | Max Loans: {$user->max_loans} | Active: {$activeLoans} | Denda: Rp " . number_format((float)$user->total_fines, 0));
                                    }
                                }
                            })
                            ->helperText('Cari berdasarkan nama, NIM, atau email'),

                        Forms\Components\Placeholder::make('user_info')
                            ->label('Info Peminjam')
                            ->content(fn($get) => $get('user_info') ?? 'Pilih peminjam untuk melihat info'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Pilih Buku')
                    ->schema([
                        Forms\Components\Select::make('book_item_id')
                            ->label('Item Buku (Copy)')
                            ->options(function () {
                                return BookItem::with('book')
                                    ->where('status', 'available')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => "{$item->book->title} - {$item->barcode} ({$item->condition})"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Hanya menampilkan buku yang tersedia')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $item = BookItem::with('book')->find($state);
                                    if ($item) {
                                        $set('book_info', "Buku: {$item->book->title} | Penulis: {$item->book->author} | Lokasi: {$item->current_location}");
                                    }
                                }
                            }),

                        Forms\Components\Placeholder::make('book_info')
                            ->label('Info Buku')
                            ->content(fn($get) => $get('book_info') ?? 'Pilih buku untuk melihat info'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Tanggal Peminjaman')
                    ->schema([
                        Forms\Components\DatePicker::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->maxDate(now()),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required()
                            ->minDate(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $loanDate = $get('loan_date');
                                $userId = $get('user_id');

                                if ($loanDate && $state && $userId) {
                                    $user = User::find($userId);
                                    $maxDays = $user && $user->isDosen() ? 30 : 14;

                                    $diffDays = \Carbon\Carbon::parse($loanDate)->diffInDays($state);

                                    if ($diffDays > $maxDays) {
                                        $set('due_date', \Carbon\Carbon::parse($loanDate)->addDays($maxDays));
                                        \Filament\Notifications\Notification::make()
                                            ->title('Durasi peminjaman disesuaikan')
                                            ->body("Maksimal durasi untuk user ini adalah {$maxDays} hari")
                                            ->warning()
                                            ->send();
                                    }
                                }
                            })
                            ->helperText(function ($get) {
                                $userId = $get('user_id');
                                if ($userId) {
                                    $user = User::find($userId);
                                    $maxDays = $user && $user->isDosen() ? 30 : 14;
                                    return "Maksimal {$maxDays} hari untuk user ini";
                                }
                                return 'Mahasiswa: max 14 hari, Dosen: max 30 hari';
                            }),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (optional)')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('processed_by')
                            ->default(Auth::id()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable(['name', 'nim'])
                    ->sortable()
                    ->description(fn(Loan $record) => $record->user->nim),

                Tables\Columns\TextColumn::make('bookItem.book.title')
                    ->label('Buku')
                    ->searchable()
                    ->limit(40)
                    ->wrap()
                    ->description(fn(Loan $record) => $record->bookItem->barcode),

                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn(Loan $record) => $record->isOverdue() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('days_until_due')
                    ->label('Sisa Hari')
                    ->badge()
                    ->color(fn(Loan $record) => match (true) {
                        $record->return_date !== null => 'gray',
                        $record->days_until_due < 0 => 'danger',
                        $record->days_until_due <= 2 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(
                        fn(Loan $record) =>
                        $record->return_date ? 'Returned' : ($record->days_until_due < 0 ? 'OVERDUE ' . abs($record->days_until_due) . 'd' : $record->days_until_due . ' hari')
                    ),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'info' => 'extended',
                        'danger' => 'overdue',
                        'gray' => 'returned',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'extended' => 'Diperpanjang',
                        'overdue' => 'Terlambat',
                        'returned' => 'Dikembalikan',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_extended')
                    ->label('Extended')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fine_amount')
                    ->label('Denda')
                    ->money('IDR')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'extended' => 'Diperpanjang',
                        'overdue' => 'Terlambat',
                        'returned' => 'Dikembalikan',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Peminjam')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->label('Hanya Terlambat')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'overdue')),

                Tables\Filters\Filter::make('has_fine')
                    ->label('Ada Denda')
                    ->query(fn(Builder $query): Builder => $query->where('fine_amount', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(Loan $record) => in_array($record->status, ['active', 'overdue', 'extended'])),

                Tables\Actions\Action::make('extend')
                    ->label('Perpanjang')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->visible(fn(Loan $record) => $record->canBeExtended())
                    ->requiresConfirmation()
                    ->modalHeading('Perpanjang Peminjaman')
                    ->modalDescription(fn(Loan $record) => "Perpanjang peminjaman buku \"{$record->bookItem->book->title}\" selama 7 hari?")
                    ->action(function (Loan $record) {
                        if ($record->extend(7)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Peminjaman diperpanjang')
                                ->body('Jatuh tempo baru: ' . \Carbon\Carbon::parse($record->due_date)->format('d M Y'))
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Tidak dapat diperpanjang')
                                ->body('Buku sudah diperpanjang atau ada booking')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn(Loan $record) => in_array($record->status, ['active', 'overdue', 'extended']))
                    ->form([
                        Forms\Components\Select::make('return_condition')
                            ->label('Kondisi Buku')
                            ->options([
                                'excellent' => 'Sangat Baik',
                                'good' => 'Baik',
                                'fair' => 'Cukup',
                                'poor' => 'Buruk',
                                'damaged' => 'Rusak',
                            ])
                            ->required()
                            ->default('good'),

                        Forms\Components\Textarea::make('return_notes')
                            ->label('Catatan Pengembalian')
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data) {
                        $record->processReturn(
                            $data['return_condition'],
                            $data['return_notes'] ?? null,
                            Auth::id()
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Buku berhasil dikembalikan')
                            ->body($record->fine_amount > 0 ? 'Denda: Rp ' . number_format($record->fine_amount, 0) : 'Tidak ada denda')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('loan_date', 'desc');
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['active', 'overdue', 'extended'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'overdue')->count();
        return $overdueCount > 0 ? 'danger' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'bookItem.book']);
    }
}
