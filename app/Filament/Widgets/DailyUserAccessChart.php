<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DailyUserAccessChart extends ChartWidget
{
    public function getHeading(): string
    {
        return 'Akses User Harian (7 Hari Terakhir)';
    }

    protected function getPollingInterval(): ?string
    {
        return '30s';
    }

    protected function getData(): array
    {
        $dates = [];
        $userCounts = [];
        $sessionCounts = [];

        // Generate last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates[] = $date->format('d/m');
            
            // Count unique users who had sessions on this date
            $userCount = DB::table('sessions')
                ->where('last_activity', '>=', $date->startOfDay()->timestamp)
                ->where('last_activity', '<=', $date->endOfDay()->timestamp)
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count();
                
            $userCounts[] = $userCount;

            // Count total sessions on this date
            $sessionCount = DB::table('sessions')
                ->where('last_activity', '>=', $date->startOfDay()->timestamp)
                ->where('last_activity', '<=', $date->endOfDay()->timestamp)
                ->count();
                
            $sessionCounts[] = $sessionCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Unique Users',
                    'data' => $userCounts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Total Sessions',
                    'data' => $sessionCounts,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}