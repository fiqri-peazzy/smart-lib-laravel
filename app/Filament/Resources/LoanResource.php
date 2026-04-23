<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\BookItem;
use App\Models\Loan;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Services\PaymentService;
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

    protected static ?string $modelLabel = 'Peminjaman';

    protected static ?string $pluralModelLabel = 'Peminjaman';

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
                                        $set('user_info', "Credit Score: {$user->credit_score} | Max Loans: {$user->max_loans} | Active: {$activeLoans} | Denda: Rp " . number_format((float) $user->total_fines, 0));
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
                                        $set('due_date', \Carbon\Carbon::parse($loanDate)->addDays((int) $maxDays));
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
                    ->sortable()
                    ->placeholder('Belum diambil')
                    ->color(fn(Loan $record) => $record->loan_date === null ? 'warning' : null),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Belum diambil')
                    ->color(fn(Loan $record) => $record->isOverdue() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('days_until_due')
                    ->label('Sisa Hari')
                    ->badge()
                    ->color(fn(Loan $record) => match (true) {
                        $record->status === 'pending_pickup' => 'warning',
                        $record->return_date !== null => 'gray',
                        $record->days_until_due < 0 => 'danger',
                        $record->days_until_due <= 2 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(
                        fn(Loan $record) =>
                        $record->status === 'pending_pickup' ? 'Pending Pickup' : ($record->return_date ? 'Returned' : ($record->days_until_due < 0 ? 'OVERDUE ' . abs($record->days_until_due) . 'd' : $record->days_until_due . ' hari'))
                    ),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending_pickup',
                        'success' => 'active',
                        'info' => 'extended',
                        'danger' => 'overdue',
                        'gray' => 'returned',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending_pickup' => 'Pending Pickup',
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

                Tables\Columns\TextColumn::make('pickup_deadline')
                    ->label('Deadline Pickup')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable()
                    ->color(fn(Loan $record) => $record->isPickupExpired() ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending_pickup' => 'Pending Pickup',
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

                Tables\Filters\Filter::make('pending_pickup')
                    ->label('Pending Pickup')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'pending_pickup')),

                Tables\Filters\Filter::make('overdue')
                    ->label('Hanya Terlambat')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'overdue')),

                Tables\Filters\Filter::make('has_fine')
                    ->label('Ada Denda')
                    ->query(fn(Builder $query): Builder => $query->where('fine_amount', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // CONFIRM PICKUP ACTION (NEW)
                Tables\Actions\Action::make('confirmPickup')
                    ->label('Konfirmasi Pengambilan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Loan $record) => $record->status === 'pending_pickup')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengambilan Buku')
                    ->modalDescription(fn(Loan $record) => "Konfirmasi bahwa {$record->user->name} telah mengambil buku \"{$record->bookItem->book->title}\"?")
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->form([
                        Forms\Components\DatePicker::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->maxDate(now()),

                        Forms\Components\Select::make('loan_duration')
                            ->label('Durasi Peminjaman')
                            ->options([
                                7 => '7 hari',
                                14 => '14 hari (default)',
                                21 => '21 hari',
                                30 => '30 hari (dosen)',
                            ])
                            ->default(14)
                            ->required()
                            ->helperText('Pilih durasi sesuai kebijakan'),
                    ])
                    ->action(function (Loan $record, array $data) {
                        $record->confirmPickup(
                            processedBy: Auth::id(),
                            loanDays: (int) ($data['loan_duration'] ?? 14)
                        );

                        // Update loan_date if specified
                        if (isset($data['loan_date'])) {
                            $record->update([
                                'loan_date' => $data['loan_date'],
                                'due_date' => \Carbon\Carbon::parse($data['loan_date'])->addDays((int) ($data['loan_duration'] ?? 14)),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Pickup Berhasil Dikonfirmasi')
                            ->body("Loan untuk {$record->user->name} telah aktif. Jatuh tempo: " . $record->due_date->format('d M Y'))
                            ->success()
                            ->send();
                    }),

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

                // CANCEL PENDING PICKUP ACTION (NEW)
                Tables\Actions\Action::make('cancelPendingPickup')
                    ->label('Batalkan Request')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Loan $record) => $record->status === 'pending_pickup')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Request Peminjaman')
                    ->modalDescription('Request peminjaman akan dibatalkan dan buku dikembalikan ke status available.')
                    ->form([
                        Forms\Components\Textarea::make('cancel_reason')
                            ->label('Alasan Pembatalan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data) {
                        $record->cancelPendingPickup($data['cancel_reason'] ?? 'Dibatalkan oleh staff');

                        \Filament\Notifications\Notification::make()
                            ->title('Request Dibatalkan')
                            ->body('Peminjaman telah dibatalkan dan buku tersedia kembali')
                            ->success()
                            ->send();
                    }),

                // MANAJEMEN DENDA
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('generateQRIS')
                        ->label('Buat QRIS')
                        ->icon('heroicon-o-qr-code')
                        ->color('info')
                        ->visible(fn(\App\Models\Loan $record) => $record->fine !== null && $record->fine->status === 'unpaid')
                        ->requiresConfirmation()
                        ->modalHeading('Generate QRIS Payment')
                        ->modalDescription(fn(\App\Models\Loan $record) => "Generate QRIS untuk pembayaran denda Rp " . number_format((float)$record->fine->amount, 0, ',', '.'))
                        ->modalSubmitActionLabel('Generate QRIS')
                        ->action(function (\App\Models\Loan $record) {
                            try {
                                $paymentService = app(\App\Services\PaymentService::class);
                                $transaction = $paymentService->generateQRIS($record->fine);

                                \Filament\Notifications\Notification::make()
                                    ->title('Pembayaran Dibuat')
                                    ->body("Silakan selesaikan pembayaran melalui portal Midtrans.")
                                    ->success()
                                    ->send();

                                $snapUrl = data_get($transaction->metadata, 'snap_url');
                                if ($snapUrl) {
                                    return redirect()->away($snapUrl);
                                }
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Gagal Generate QRIS')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('generateVA')
                        ->label('Buat VA')
                        ->icon('heroicon-o-credit-card')
                        ->color('warning')
                        ->visible(fn(\App\Models\Loan $record) => $record->fine !== null && $record->fine->status === 'unpaid')
                        ->form([
                            Forms\Components\Select::make('bank')
                                ->label('Bank')
                                ->options([
                                    'bca' => 'BCA',
                                    'bni' => 'BNI',
                                    'bri' => 'BRI',
                                    'mandiri' => 'Mandiri',
                                    'permata' => 'Permata',
                                ])
                                ->required()
                                ->default('bca')
                                ->helperText('Pilih bank untuk Virtual Account'),
                        ])
                        ->action(function (\App\Models\Loan $record, array $data) {
                            try {
                                $paymentService = app(\App\Services\PaymentService::class);
                                $transaction = $paymentService->generateVA($record->fine, $data['bank']);

                                \Filament\Notifications\Notification::make()
                                    ->title('VA Berhasil Dibuat')
                                    ->body("Silakan selesaikan pembayaran melalui portal Midtrans.")
                                    ->success()
                                    ->send();

                                $snapUrl = data_get($transaction->metadata, 'snap_url');
                                if ($snapUrl) {
                                    return redirect()->away($snapUrl);
                                }
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Gagal Generate VA')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('checkPaymentStatus')
                        ->label('Cek Status Pembayaran')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->visible(function (\App\Models\Loan $record) {
                            return $record->fine !== null && $record->fine->status === 'unpaid' &&
                                PaymentTransaction::where('fine_id', $record->fine->id)
                                ->where('status', 'pending')
                                ->exists();
                        })
                        ->action(function (\App\Models\Loan $record) {
                            try {
                                $transaction = PaymentTransaction::where('fine_id', $record->fine->id)
                                    ->where('status', 'pending')
                                    ->latest()
                                    ->first();

                                if (!$transaction) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Tidak Ada Transaksi Pending')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                $paymentService = app(\App\Services\PaymentService::class);
                                $status = $paymentService->checkPaymentStatus($transaction);

                                if ($status['success']) {
                                    $transactionStatus = $status['status'];

                                    if (in_array($transactionStatus, ['settlement', 'capture'])) {
                                        $transactionId = data_get($status['data'], 'transaction_id');
                                        $transaction->markAsSuccess($transactionId);

                                        $record->fine->processPayment(
                                            amount: (float) $transaction->amount,
                                            method: $transaction->payment_method,
                                            reference: $transactionId
                                        );

                                        \Filament\Notifications\Notification::make()
                                            ->title('Pembayaran Berhasil')
                                            ->body("Pembayaran telah diverifikasi dan denda telah lunas.")
                                            ->success()
                                            ->send();
                                    } else {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Status Pembayaran')
                                            ->body("Status saat ini: " . strtoupper($transactionStatus))
                                            ->info()
                                            ->send();
                                    }
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Gagal Check Status')
                                        ->body($status['message'])
                                        ->danger()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('payFineManual')
                        ->label('Bayar Manual')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->visible(fn(\App\Models\Loan $record) => $record->fine !== null && $record->fine->status === 'unpaid')
                        ->form([
                            Forms\Components\TextInput::make('paid_amount')
                                ->label('Jumlah Dibayar')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->default(fn(\App\Models\Loan $record) => $record->fine->amount)
                                ->minValue(0)
                                ->helperText(fn(\App\Models\Loan $record) => 'Total denda: Rp ' . number_format((float) $record->fine->amount, 0)),

                            Forms\Components\Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Tunai',
                                    'transfer' => 'Transfer Bank',
                                    'other' => 'Lainnya',
                                ])
                                ->required()
                                ->default('cash'),

                            Forms\Components\TextInput::make('payment_reference')
                                ->label('Referensi Pembayaran')
                                ->placeholder('No. Bukti / Referensi (optional)'),

                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->rows(2),
                        ])
                        ->action(function (\App\Models\Loan $record, array $data) {
                            $record->fine->processPayment(
                                $data['paid_amount'],
                                $data['payment_method'],
                                $data['payment_reference'] ?? null,
                                Auth::id()
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Pembayaran Berhasil')
                                ->body('Denda telah dibayar. Credit score user telah diupdate.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('waiveFine')
                        ->label('Bebaskan')
                        ->icon('heroicon-o-shield-check')
                        ->color('info')
                        ->visible(fn(\App\Models\Loan $record) => collect(Auth::user()->roles->pluck('name'))->contains('admin') && $record->fine !== null && $record->fine->status === 'unpaid')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('waive_reason')
                                ->label('Alasan Pembebasan')
                                ->required()
                                ->rows(3)
                                ->placeholder('Jelaskan alasan pembebasan denda...'),
                        ])
                        ->action(function (\App\Models\Loan $record, array $data) {
                            $record->fine->waive($data['waive_reason'], Auth::id());

                            \Filament\Notifications\Notification::make()
                                ->title('Denda Dibebaskan')
                                ->body('Denda telah dibebaskan oleh admin.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('viewFineTransactions')
                        ->label('Log Transaksi')
                        ->icon('heroicon-o-list-bullet')
                        ->color('gray')
                        ->visible(fn(\App\Models\Loan $record) => $record->fine !== null && PaymentTransaction::where('fine_id', $record->fine->id)->exists())
                        ->modalHeading('Riwayat Transaksi Gateway')
                        ->modalContent(fn(\App\Models\Loan $record) => view('filament.resources.fines.transactions-modal', [
                            'transactions' => PaymentTransaction::where('fine_id', $record->fine->id)->latest()->get()
                        ])),
                ])
                    ->label('Kelola Denda')
                    ->icon('heroicon-o-banknotes')
                    ->color('danger')
                    ->visible(fn(\App\Models\Loan $record) => $record->fine !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = static::getModel()::where('status', 'pending_pickup')->count();
        $activeCount = static::getModel()::whereIn('status', ['active', 'overdue', 'extended'])->count();

        return $pendingCount > 0 ? "Pending {$pendingCount} | Active {$activeCount}" : $activeCount;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdueCount = static::getModel()::where('status', 'overdue')->count();
        $pendingCount = static::getModel()::where('status', 'pending_pickup')->count();

        if ($overdueCount > 0) return 'danger';
        if ($pendingCount > 0) return 'warning';
        return 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'bookItem.book']);
    }
}
