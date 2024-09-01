<?php

namespace App\Filament\Resources\CriticalResource\Pages;

use App\Filament\Resources\CriticalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCritical extends CreateRecord
{
    protected static string $resource = CriticalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
