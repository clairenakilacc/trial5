<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Filament\Widgets\OtherWidget;
use App\Filament\Widgets\AllEquipment;
use App\Filament\Widgets\TotalUserWidget;
//use App\Filament\Widgets\AllFacilities;
use App\Filament\Widgets\AllFacility;
use App\Filament\Widgets\AllCategory;
use App\Filament\Widgets\EquipmentCountPerCategory;
use App\Filament\Widgets\EquipmentCountPerStatus;
use App\Filament\Widgets\EquipmentCountPerFacility;
use App\Filament\Widgets\ForDisposal;
use App\Filament\Widgets\Disposed;
use App\Filament\Widgets\PersonLiable;
use App\Filament\Widgets\FacilityPerFloorLevel;












class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public int | string | array $columnSpan = 3;

    public function getWidgets(): array
    {
        return [
            TotalUserWidget::class,  // This will appear first
            AllEquipment::class,     // w/ facilities, categories
            EquipmentCountPerCategory::class,
            EquipmentCountPerStatus::class,
            EquipmentCountPerFacility::class,
            ForDisposal::class,
            Disposed::class,
            FacilityPerFloorLevel::class,
            PersonLiable::class,
            //AllFacility::class,
           // AllCategory::class,
            // AllFacilities::class,   
           //EquipmentCatStat::class,
        ];
    }

    public function getColumns(): int
    {
        return 3
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        ; // Adjust the grid to 2 columns to place widgets side by side
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            //TextInput::make('name'),
            DatePicker::make('startDate'),
            DatePicker::make('endDate'),
            //Toggle::make('active'),
        ])->columns(2);
    }
}
