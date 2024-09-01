<?php

namespace App\Filament\Resources\CriticalResource\Pages;

use App\Filament\Resources\CriticalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCritical extends EditRecord
{
    protected static string $resource = CriticalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
