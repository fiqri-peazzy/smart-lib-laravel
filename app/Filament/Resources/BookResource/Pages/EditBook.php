<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('manage_items')
                ->label('Kelola Items')
                ->icon('heroicon-o-squares-2x2')
                ->color('info')
                ->url(fn() => route('filament.admin.resources.book-items.index', [
                    'tableFilters' => [
                        'book_id' => ['value' => $this->record->id]
                    ]
                ])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
