<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Facility;
use Filament\Support\Enums\IconPosition;


class AllFacilities extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make ( '', Facility::count())
                ->description('CCIS Facilities ')
                ->descriptionIcon('heroicon-m-cube',IconPosition::Before )
                //->chart([1,3,5,10,20])
                ->color('success')
        ];
    }
}
