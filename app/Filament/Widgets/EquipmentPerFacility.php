<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Equipment;
use App\Models\Facility;

class EquipmentPerFacility extends ChartWidget
{
    protected static ?string $heading = 'Equipments Per Facility';

    protected function getData(): array
    {

        $equipmentCounts = Equipment::selectRaw('facility_id, COUNT(*) as count')
            ->groupBy('facility_id')
            ->pluck('count', 'facility_id')
            ->toArray();

        $facilities = Facility::all();

        // Prepare labels and data for the chart
        $labels = [];
        $data = [];

        foreach ($facilities as $facility) {
            $labels[] = $facility->name;  // Assuming the 'name' field is the facility name
            $data[] = $equipmentCounts[$facility->id] ?? 0;  // Default to 0 if no equipment is found for that facility
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Equipments',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(201, 203, 207, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(201, 203, 207, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
