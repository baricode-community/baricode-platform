<?php

namespace App\Filament\Widgets;

use App\Models\Auth\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyUserRegistrationChart extends ChartWidget
{
    public function getHeading(): string
    {
        return 'Registrasi User per Bulan (6 Bulan Terakhir)';
    }

    protected function getPollingInterval(): ?string
    {
        return '60s';
    }

    protected function getData(): array
    {
        $months = [];
        $registrations = [];

        // Generate last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            // Count users registered in this month
            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->whereNull('deleted_at')
                ->count();
                
            $registrations[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'User Baru',
                    'data' => $registrations,
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',   // red
                        'rgba(245, 158, 11, 0.8)',  // amber
                        'rgba(34, 197, 94, 0.8)',   // green
                        'rgba(59, 130, 246, 0.8)',  // blue
                        'rgba(168, 85, 247, 0.8)',  // violet
                        'rgba(236, 72, 153, 0.8)',  // pink
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                    'enabled' => true,
                ],
            ],
        ];
    }
}