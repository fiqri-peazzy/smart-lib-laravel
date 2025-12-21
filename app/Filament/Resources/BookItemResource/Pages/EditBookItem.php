<?php

namespace App\Filament\Resources\BookItemResource\Pages;

use App\Filament\Resources\BookItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookItem extends EditRecord
{
    protected static string $resource = BookItemResource::class;

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
}
