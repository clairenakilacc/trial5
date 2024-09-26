<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Equipment;
use App\Models\Category;

class EquipmentCatStat extends ChartWidget
{
    protected static ?string $heading = 'Equipment Statistics';

    protected function getData(): array
    {
        return [
            'statusChart' => $this->getStatusData(),
            'categoryChart' => $this->getCategoryData(),
        ];
    }

    protected function getStatusData(): array
    {
        // Fetch equipment status counts
        $statusCounts = Equipment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Prepare labels and data for the status chart
        $statusLabels = array_keys($statusCounts);
        $statusData = array_values($statusCounts);

        return [
            'datasets' => [
                [
                    'label' => 'Equipment Count by Status',
                    'data' => $statusData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $statusLabels,
        ];
    }

    protected function getCategoryData(): array
    {
        // Fetch categories and their equipment count
        $categoryCounts = Equipment::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        $categories = Category::all();

        // Prepare labels and data for the category chart
        $categoryLabels = [];
        $categoryData = [];

        foreach ($categories as $category) {
            $categoryLabels[] = $category->description; // Assuming 'description' is the category name
            $categoryData[] = $categoryCounts[$category->id] ?? 0; // Default to 0 if no equipment in that category
        }

        return [
            'datasets' => [
                [
                    'label' => 'Equipment Count by Category',
                    'data' => $categoryData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $categoryLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
