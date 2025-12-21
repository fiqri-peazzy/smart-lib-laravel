<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationLabel = 'Booking Buku';

    protected static ?string $navigationGroup = 'Manajemen Perpustakaan';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Booking')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'nim', 'email'])
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('book_id')
                            ->label('Buku')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih buku yang ingin di-booking'),

                        Forms\Components\DatePicker::make('booking_date')
                            ->label('Tanggal Booking')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Kadaluarsa')
                            ->default(now()->addDays(3))
                            ->minDate(now())
                            ->helperText('Booking akan expired setelah tanggal ini'),

                        Forms\Components\Toggle::make('is_priority')
                            ->label('Priority Booking')
                            ->helperText('Aktifkan untuk booking priority (dosen)'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3),
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
                    ->description(fn(Booking $record) => $record->user->nim),

                Tables\Columns\TextColumn::make('book.title')
                    ->label('Buku')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_priority')
                    ->label('Priority')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Tgl Booking')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Kadaluarsa')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn(Booking $record) => $record->isExpired() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'notified',
                        'success' => 'fulfilled',
                        'gray' => 'cancelled',
                        'danger' => 'expired',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'notified' => 'Sudah Diberitahu',
                        'fulfilled' => 'Terpenuhi',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Kadaluarsa',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('notified_at')
                    ->label('Diberitahu')
                    ->dateTime('d M Y H:i')
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
                        'pending' => 'Menunggu',
                        'notified' => 'Sudah Diberitahu',
                        'fulfilled' => 'Terpenuhi',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Kadaluarsa',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_priority')
                    ->label('Priority')
                    ->placeholder('Semua')
                    ->trueLabel('Priority')
                    ->falseLabel('Non-Priority'),

                Tables\Filters\Filter::make('expired')
                    ->label('Sudah Expired')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('status', 'pending')
                            ->where('expires_at', '<', now())
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(Booking $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('notify')
                    ->label('Beritahu')
                    ->icon('heroicon-o-bell')
                    ->color('info')
                    ->visible(fn(Booking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Beritahu User')
                    ->modalDescription(fn(Booking $record) => "Kirim notifikasi ke {$record->user->name} bahwa buku tersedia?")
                    ->action(function (Booking $record) {
                        $record->notify();

                        \Filament\Notifications\Notification::make()
                            ->title('Notifikasi terkirim')
                            ->body('User akan diberitahu bahwa buku sudah tersedia')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('fulfill')
                    ->label('Penuhi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Booking $record) => in_array($record->status, ['pending', 'notified']))
                    ->requiresConfirmation()
                    ->action(function (Booking $record) {
                        $record->fulfill();

                        \Filament\Notifications\Notification::make()
                            ->title('Booking terpenuhi')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Booking $record) => in_array($record->status, ['pending', 'notified']))
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Pembatalan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Booking $record, array $data) {
                        $record->cancel($data['reason']);

                        \Filament\Notifications\Notification::make()
                            ->title('Booking dibatalkan')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('cancel_multiple')
                        ->label('Batalkan Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Alasan Pembatalan')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'notified'])) {
                                    $record->cancel($data['reason']);
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Booking dibatalkan')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('booking_date', 'desc');
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'book']);
    }
}
