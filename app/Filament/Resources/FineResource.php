<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FineResource\Pages;
use App\Models\Fine;
use App\Models\PaymentTransaction;
use App\Services\PaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Denda';

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Denda')
                    ->schema([
                        Forms\Components\Select::make('loan_id')
                            ->label('Peminjaman')
                            ->relationship('loan', 'id')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Denda')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('days_overdue')
                            ->label('Hari Terlambat')
                            ->numeric()
                            ->suffix('hari')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'unpaid' => 'Belum Dibayar',
                                'paid' => 'Sudah Dibayar',
                                'waived' => 'Dibebaskan',
                            ])
                            ->disabled()
                            ->dehydrated(false),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(['name', 'nim'])
                    ->sortable()
                    ->description(fn (Fine $record) => $record->user->nim),

                Tables\Columns\TextColumn::make('loan.bookItem.book.title')
                    ->label('Buku')
                    ->searchable()
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('Terlambat')
                    ->sortable()
                    ->suffix(' hari')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah Denda')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Sisa')
                    ->money('IDR')
                    ->color('danger')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'danger' => 'unpaid',
                        'success' => 'paid',
                        'info' => 'waived',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Lunas',
                        'waived' => 'Dibebaskan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tgl Bayar')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Lunas',
                        'waived' => 'Dibebaskan',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('high_fines')
                    ->label('Denda Tinggi (> Rp 25.000)')
                    ->query(fn (Builder $query): Builder => $query->where('amount', '>', 25000)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // PAYMENT GATEWAY ACTIONS

                Tables\Actions\Action::make('generateQRIS')
                    ->label('Generate QRIS')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->visible(fn (Fine $record) => $record->status === 'unpaid')
                    ->requiresConfirmation()
                    ->modalHeading('Generate QRIS Payment')
                    ->modalDescription(fn (Fine $record) => "Generate QRIS untuk pembayaran denda Rp " . number_format((float)$record->amount, 0, ',', '.'))
                    ->modalSubmitActionLabel('Generate QRIS')
                    ->action(function (Fine $record) {
                        try {
                            $paymentService = app(PaymentService::class);
                            $transaction = $paymentService->generateQRIS($record);

                            \Filament\Notifications\Notification::make()
                                ->title('Pembayaran Dibuat')
                                ->body("Silakan selesaikan pembayaran melalui portal Midtrans.")
                                ->success()
                                ->send();

                            // Redirect ke Midtrans Snap URL
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
                    ->label('Generate VA')
                    ->icon('heroicon-o-credit-card')
                    ->color('warning')
                    ->visible(fn (Fine $record) => $record->status === 'unpaid')
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
                    ->action(function (Fine $record, array $data) {
                        try {
                            $paymentService = app(PaymentService::class);
                            $transaction = $paymentService->generateVA($record, $data['bank']);

                            \Filament\Notifications\Notification::make()
                                ->title('VA Berhasil Dibuat')
                                ->body("Silakan selesaikan pembayaran melalui portal Midtrans.")
                                ->success()
                                ->send();

                            // Redirect ke Midtrans Snap URL
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
                    ->label('Check Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(function (Fine $record) {
                        // Show jika ada pending transaction
                        return $record->status === 'unpaid' &&
                               PaymentTransaction::where('fine_id', $record->id)
                                   ->where('status', 'pending')
                                   ->exists();
                    })
                    ->action(function (Fine $record) {
                        try {
                            $transaction = PaymentTransaction::where('fine_id', $record->id)
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

                            $paymentService = app(PaymentService::class);
                            $status = $paymentService->checkPaymentStatus($transaction);

                            if ($status['success']) {
                                $transactionStatus = $status['status'];
                                
                                // Jika sukses, update transaksi dan fine
                                if (in_array($transactionStatus, ['settlement', 'capture'])) {
                                    $transactionId = data_get($status['data'], 'transaction_id');
                                    $transaction->markAsSuccess($transactionId);
                                    
                                    // Proses pembayaran denda
                                    $record->processPayment(
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

                // MANUAL PAYMENT ACTIONS

                Tables\Actions\Action::make('pay')
                    ->label('Bayar Manual')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (Fine $record) => $record->status === 'unpaid')
                    ->form([
                        Forms\Components\TextInput::make('paid_amount')
                            ->label('Jumlah Dibayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(fn (Fine $record) => $record->amount)
                            ->minValue(0)
                            ->helperText(fn (Fine $record) => 'Total denda: Rp ' . number_format((float) $record->amount, 0)),

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
                    ->action(function (Fine $record, array $data) {
                        $record->processPayment(
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

                Tables\Actions\Action::make('waive')
                    ->label('Bebaskan')
                    ->icon('heroicon-o-shield-check')
                    ->color('info')
                    ->visible(fn (Fine $record) => $record->status === 'unpaid' && Auth::user()->hasRole('admin'))
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('waive_reason')
                            ->label('Alasan Pembebasan')
                            ->required()
                            ->rows(3)
                            ->placeholder('Jelaskan alasan pembebasan denda...'),
                    ])
                    ->action(function (Fine $record, array $data) {
                        $record->waive($data['waive_reason'], Auth::id());

                        \Filament\Notifications\Notification::make()
                            ->title('Denda Dibebaskan')
                            ->body('Denda telah dibebaskan oleh admin.')
                            ->success()
                            ->send();
                    }),

                // VIEW PAYMENT TRANSACTIONS

                Tables\Actions\Action::make('viewTransactions')
                    ->label('Log Transaksi')
                    ->icon('heroicon-o-list-bullet')
                    ->color('gray')
                    ->visible(fn (Fine $record) => PaymentTransaction::where('fine_id', $record->id)->exists())
                    ->modalHeading('Riwayat Transaksi Gateway')
                    ->modalContent(fn (Fine $record) => view('filament.resources.fines.transactions-modal', [
                        'transactions' => PaymentTransaction::where('fine_id', $record->id)->latest()->get()
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_pay')
                        ->label('Bayar Terpilih')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Tunai',
                                    'transfer' => 'Transfer',
                                    'other' => 'Lainnya',
                                ])
                                ->required()
                                ->default('cash'),

                            Forms\Components\TextInput::make('payment_reference')
                                ->label('Referensi Pembayaran')
                                ->placeholder('No. Bukti / Referensi (optional)'),
                        ])
                        ->action(function ($records, array $data) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'unpaid') {
                                    $record->processPayment(
                                        $record->amount,
                                        $data['payment_method'],
                                        $data['payment_reference'] ?? null,
                                        Auth::id()
                                    );
                                    $count++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Pembayaran Berhasil')
                                ->body("{$count} denda telah dibayar")
                                ->success()
                                ->send();
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
            'index' => Pages\ListFines::route('/'),
            'view' => Pages\ViewFine::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', 'unpaid')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unpaidCount = static::$model::where('status', 'unpaid')->count();
        return $unpaidCount > 0 ? 'danger' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'loan.bookItem.book']);
    }
}