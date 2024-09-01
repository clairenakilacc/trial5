<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Borrow;
use Carbon\Carbon;

class UserStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        // User statistics
        $previousTotalUsers = User::whereDate('created_at', Carbon::yesterday())->count();
        $currentTotalUsers = User::count();
        $userIncrease = $currentTotalUsers - $previousTotalUsers;

        $userRegistrationsLast7Days = User::where('created_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            })
            ->map(function ($day) {
                return $day->count();
            });

        $userChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $userChartData[] = $userRegistrationsLast7Days->get($date, 0);
        }

        // Borrow statistics
        $borrowRecordsLast7Days = Borrow::where('created_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            })
            ->map(function ($day) {
                return $day->count();
            });

        $borrowChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $borrowChartData[] = $borrowRecordsLast7Days->get($date, 0);
        }

        $totalBorrowRecords = Borrow::count();

        return [
            Stat::make('Total Users', $currentTotalUsers)
                ->chart($userChartData)
                ->description("{$userIncrease} New Users")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Borrow Records', $totalBorrowRecords)
                ->chart($borrowChartData)
                ->description('Borrow activity over the last 7 days')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
