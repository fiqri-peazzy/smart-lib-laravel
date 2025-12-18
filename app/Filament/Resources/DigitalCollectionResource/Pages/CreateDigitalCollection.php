<?php

namespace App\Filament\Resources\DigitalCollectionResource\Pages;

use App\Filament\Resources\DigitalCollectionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreateDigitalCollection extends CreateRecord
{
    protected static string $resource = DigitalCollectionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set uploader
        $data['uploaded_by'] = Auth::id();

        if (isset($data['file_path'])) {
            $data['file_type'] = pathinfo($data['file_path'], PATHINFO_EXTENSION);

            // FileUpload stores file in default disk (public usually)
            if (Storage::disk('public')->exists($data['file_path'])) {
                $data['file_size'] = Storage::disk('public')->size($data['file_path']);
            }
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Koleksi digital berhasil ditambahkan!';
    }
}
