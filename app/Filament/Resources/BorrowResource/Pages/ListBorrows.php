<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListBorrows extends ListRecords
{
    use  \EightyNine\Approvals\Traits\HasApprovalHeaderActions;
    protected static string $resource = BorrowResource::class;
    protected ?string $heading = 'Transferred to Borrows';

    protected function getHeaderActions(): array
    {
        return [];
    }
    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()
            ->orderBy('created_at', 'desc'); // Order by the latest entries first
    }
}
