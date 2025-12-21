<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function afterCreate(): void
    {
        $this->redirect(route('filament.admin.resources.book-items.create', ['book' => $this->record->id]));
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Buku berhasil ditambahkan! Silakan tambahkan item/copy buku.';
    }
}
