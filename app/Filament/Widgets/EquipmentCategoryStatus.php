<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Equipment;
use App\Models\Category;

class EquipmentCategoryStatus extends ChartWidget
{
    protected static ?string $heading = 'Equipment Count per Category';
    protected static string $color = 'primary';

    protected function getData(): array
    {
        // Fetch all categories and their equipment counts
        $categories = Category::withCount('equipment')->get();

        // Extract category descriptions and equipment counts
        $labels = $categories->pluck('description')->toArray();
        $data = $categories->pluck('equipment_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => "Equipment Count",
                    'data' => $data,
                    'backgroundColor' => '#ffa500', // Customize bar color
                ],
            ],
            'labels' => $labels, // Use category descriptions as labels

            
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart type
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Rotates chart to horizontal
        ];
    }
}
