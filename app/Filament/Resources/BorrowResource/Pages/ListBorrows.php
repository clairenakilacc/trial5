<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListBorrows extends ListRecords
{
    use  \EightyNine\Approvals\Traits\HasApprovalHeaderActions;
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
