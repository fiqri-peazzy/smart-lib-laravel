<?php

namespace App\Filament\Resources\BookItemResource\Pages;

use App\Filament\Resources\BookItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookItems extends ListRecords
{
    protected static string $resource = BookItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
