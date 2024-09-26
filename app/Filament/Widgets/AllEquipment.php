<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Equipment;
use App\Models\Facility;
use App\Models\Category;


use Filament\Support\Enums\IconPosition;


class AllEquipment extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make ( '', Equipment::count())
                ->description('Total Equipment ')
                ->descriptionIcon('heroicon-m-cube',IconPosition::After )
                //->chart([1,3,5,10,20])
                ->color('success'),
             Stat::make ( '', Facility::count())
                ->description('CCIS Facilities ')
                ->descriptionIcon('heroicon-m-building-office-2',IconPosition::After )
                //->chart([1,3,5,10,20])
                ->color('info'),
            Stat::make ( '', Category::count())
                ->description('Equipment Categories ')
                ->descriptionIcon('heroicon-m-funnel',IconPosition::After )
                //->chart([1,3,5,10,20])
                ->color('primary')
        ];
    }
    
}
