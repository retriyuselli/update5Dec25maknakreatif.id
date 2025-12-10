<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserExpirationOverview extends BaseWidget
{
    // protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        try {
            // Count expired users
            $expiredUsers = User::whereNotNull('expire_date')
                ->where('expire_date', '<=', Carbon::now())
                ->count();

            // Count users expiring within 7 days
            $expiringSoonUsers = User::whereNotNull('expire_date')
                ->whereBetween('expire_date', [
                    Carbon::now(),
                    Carbon::now()->addDays(7),
                ])->count();

            // Count active users (no expire date or expire date in future)
            $activeUsers = User::where(function ($query) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', Carbon::now());
            })->count();

            // Total users count
            $totalUsers = User::count();

            return [
                Stat::make('Total User', $totalUsers)
                    ->description('Total semua pengguna')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('info'),

                Stat::make('User Aktif', $activeUsers)
                    ->description('Pengguna yang masih aktif')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),

                Stat::make('Akan Kedaluwarsa', $expiringSoonUsers)
                    ->description('Dalam 7 hari ke depan')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),

                Stat::make('Sudah Kedaluwarsa', $expiredUsers)
                    ->description('Perlu diperpanjang')
                    ->descriptionIcon('heroicon-m-x-circle')
                    ->color('danger'),
            ];
        } catch (Exception $e) {
            // Fallback jika ada error
            return [
                Stat::make('Error', 'Tidak dapat memuat data')
                    ->description('Error: '.$e->getMessage())
                    ->color('gray'),
            ];
        }
    }
}
