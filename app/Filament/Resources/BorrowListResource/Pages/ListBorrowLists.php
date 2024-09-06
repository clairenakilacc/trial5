<?php

namespace App\Filament\Resources\BorrowListResource\Pages;

use App\Filament\Resources\BorrowListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBorrowLists extends ListRecords
{
    protected static string $resource = BorrowListResource::class;

    protected ?string $heading = 'Borrow Lists';
    
    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()
            ->orderBy('created_at', 'desc'); // Order by the latest entries first
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
