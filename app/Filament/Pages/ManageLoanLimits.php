<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageLoanLimits extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string $view = 'filament.pages.manage-loan-limits';

    protected static ?string $navigationLabel = 'Batas Peminjaman';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Pengaturan Batas Peminjaman';

    protected static ?string $navigationGroup = 'Sistem';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SystemSetting::where('group', 'loan_limits')->get();

        foreach ($settings as $setting) {
            $this->data[$setting->key] = $setting->value;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Batas Pinjam Mahasiswa')
                    ->description('Atur jumlah maksimal buku yang dapat dipinjam mahasiswa.')
                    ->schema([
                        TextInput::make('loan_limit_mahasiswa')
                            ->label('Batas Maksimal Buku')
                            ->numeric()
                            ->required(),
                    ]),

                Section::make('Batas Pinjam Dosen')
                    ->description('Atur jumlah maksimal buku yang dapat dipinjam dosen.')
                    ->schema([
                        TextInput::make('loan_limit_dosen')
                            ->label('Batas Maksimal Buku')
                            ->numeric()
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                SystemSetting::set($key, $value);
            }

            Notification::make()
                ->title('Pengaturan berhasil disimpan')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan pengaturan')
                ->danger()
                ->send();
        }
    }
}
