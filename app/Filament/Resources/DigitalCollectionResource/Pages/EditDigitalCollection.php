<?php

namespace App\Filament\Resources\DigitalCollectionResource\Pages;

use App\Filament\Resources\DigitalCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDigitalCollection extends EditRecord
{
    protected static string $resource = DigitalCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Koleksi digital berhasil diupdate!';
    }
}
