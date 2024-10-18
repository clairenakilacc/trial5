<?php

namespace App\Filament\Resources\RequestListResource\Pages;

use App\Filament\Resources\RequestListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequestList extends ListRecords
{
    protected static string $resource = RequestListResource::class;
    protected ?string $heading = 'Request List';
    protected static ?string $navigationIcon = 'shopping-cart';

    // Override to remove breadcrumbs
    public function getBreadcrumbs(): array
    {
        return [];
    }

    /*protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }*/
}
