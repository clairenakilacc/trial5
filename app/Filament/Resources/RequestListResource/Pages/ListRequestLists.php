<?php

namespace App\Filament\Resources\RequestListResource\Pages;

use App\Filament\Resources\RequestListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequestLists extends ListRecords
{
    protected static string $resource = RequestListResource::class;
    protected ?string $heading = 'Request List';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
