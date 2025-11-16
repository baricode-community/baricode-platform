<?php

namespace App\Filament\Widgets;

use App\Models\Auth\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserStatsWidget extends BaseWidget
{
    protected function getPollingInterval(): ?string
    {
        return '30s';
    }

    protected function getStats(): array
    {
        $totalUsers = User::whereNull('deleted_at')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNull('deleted_at')
            ->count();
        
        $newUsersLastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereNull('deleted_at')
            ->count();

        // Calculate percentage change
        $percentageChange = $newUsersLastMonth > 0 
            ? (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 
            : 100;

        // Active users (users who have sessions in the last 7 days)
        $activeUsers = DB::table('sessions')
            ->where('last_activity', '>=', now()->subDays(7)->timestamp)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();

        // Verified users
        $verifiedUsers = User::whereNotNull('email_verified_at')
            ->whereNull('deleted_at')
            ->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('New Users This Month', $newUsersThisMonth)
                ->description($percentageChange > 0 ? "+{$percentageChange}% from last month" : "{$percentageChange}% from last month")
                ->descriptionIcon($percentageChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($percentageChange > 0 ? 'success' : 'danger'),
                
            Stat::make('Active Users (7 days)', $activeUsers)
                ->description('Users active in the last 7 days')
                ->descriptionIcon('heroicon-m-signal')
                ->color('primary'),
                
            Stat::make('Verified Users', $verifiedUsers)
                ->description('Email verified users')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),
        ];
    }
}