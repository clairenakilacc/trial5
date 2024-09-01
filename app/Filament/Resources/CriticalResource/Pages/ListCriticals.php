<?php

namespace App\Filament\Resources\CriticalResource\Pages;

use App\Filament\Resources\CriticalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCriticals extends ListRecords
{
    protected static string $resource = CriticalResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
