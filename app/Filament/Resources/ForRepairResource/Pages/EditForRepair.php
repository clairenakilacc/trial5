<?php

namespace App\Filament\Resources\ForRepairResource\Pages;

use App\Filament\Resources\ForRepairResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForRepair extends EditRecord
{
    protected static string $resource = ForRepairResource::class;

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
