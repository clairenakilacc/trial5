<?php

namespace App\Filament\Resources\MonitoringHistoryResource\Pages;

use App\Filament\Resources\MonitoringHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonitoringHistories extends ListRecords
{
    protected static string $resource = MonitoringHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
