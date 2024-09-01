<?php

namespace App\Filament\Resources\BorrowListResource\Pages;

use App\Filament\Resources\BorrowListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrowLists extends ListRecords
{
    protected static string $resource = BorrowListResource::class;

    protected ?string $heading = 'Request List';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
