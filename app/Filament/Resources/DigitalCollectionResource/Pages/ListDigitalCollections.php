<?php

namespace App\Filament\Resources\DigitalCollectionResource\Pages;

use App\Filament\Resources\DigitalCollectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDigitalCollections extends ListRecords
{
    protected static string $resource = DigitalCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
