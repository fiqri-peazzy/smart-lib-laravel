<?php

// File: app/Filament/Resources/UserResource/Pages/ListUsers.php


namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate username dari nama jika kosong
        if (empty($data['username'])) {
            $data['username'] = strtolower(str_replace(' ', '.', $data['name']));
        }

        return $data;
    }
}
