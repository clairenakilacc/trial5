<?php

namespace App\Filament\Resources\ForRepairResource\Pages;

use App\Filament\Resources\ForRepairResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateForRepair extends CreateRecord
{
    protected static string $resource = ForRepairResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
