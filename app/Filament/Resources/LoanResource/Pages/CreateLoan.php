<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate user can borrow
        $user = \App\Models\User::find($data['user_id']);

        if (!$user->canBorrow()) {
            throw new \Exception('User tidak dapat meminjam buku. Periksa denda atau status user.');
        }

        // Check max loans
        $activeLoansCount = $user->activeLoans()->count();
        if ($activeLoansCount >= $user->max_loans) {
            throw new \Exception("User sudah mencapai limit peminjaman ({$user->max_loans} buku).");
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Peminjaman berhasil dicatat!';
    }

    protected function afterCreate(): void
    {
        // Optional: Print receipt
    }
}
