<?php

namespace App\Filament\Resources\BookItemResource\Pages;

use App\Filament\Resources\BookItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBookItem extends CreateRecord
{
    protected static string $resource = BookItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Item buku berhasil ditambahkan dan QR Code telah di-generate!';
    }
}
