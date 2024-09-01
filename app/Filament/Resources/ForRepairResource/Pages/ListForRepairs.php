<?php

namespace App\Filament\Resources\ForRepairResource\Pages;

use App\Filament\Resources\ForRepairResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForRepairs extends ListRecords
{
    protected static string $resource = ForRepairResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
