<?php

namespace App\Filament\Resources\AccountManagerTargets\Widgets;

use App\Models\AccountManagerTarget;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AmOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Query base untuk data target (hanya dari Account Manager aktif)
        $query = AccountManagerTarget::query()
            ->where('year', $currentYear)
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($subQ) {
                    $subQ->where('name', 'Account Manager');
                })
                    ->where('status', 'active');
            });

        // Untuk saat ini tampilkan semua data dari Account Manager aktif
        // Uncomment baris di bawah jika ingin filter berdasarkan user tertentu
        // $query->where('user_id', $user?->id ?? 0);

        // Target Bulan Ini
        $currentMonthTarget = (clone $query)
            ->where('month', $currentMonth)
            ->sum('target_amount');

        // Achievement Bulan Ini
        $currentMonthAchievement = (clone $query)
            ->where('month', $currentMonth)
            ->sum('achieved_amount');

        // Target Tahun Ini
        $yearlyTarget = (clone $query)->sum('target_amount');

        // Achievement Tahun Ini
        $yearlyAchievement = (clone $query)->sum('achieved_amount');

        // Hitung persentase achievement bulan ini
        $monthlyPercentage = $currentMonthTarget > 0
            ? round(($currentMonthAchievement / $currentMonthTarget) * 100, 1)
            : 0;

        // Hitung persentase achievement tahunan
        $yearlyPercentage = $yearlyTarget > 0
            ? round(($yearlyAchievement / $yearlyTarget) * 100, 1)
            : 0;

        // Jumlah Account Manager aktif (role Account Manager + status aktif)
        $activeAccountManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'Account Manager');
        })
            ->where('status', 'active')
            ->count();

        // Target yang sudah tercapai bulan ini (sudah terfilter Account Manager aktif dari query base)
        $achievedTargetsThisMonth = (clone $query)
            ->where('month', $currentMonth)
            ->whereRaw('achieved_amount >= target_amount')
            ->count();

        return [
            Stat::make('Target Bulan Ini', ' '.number_format($currentMonthTarget, 0, ',', '.'))
                ->description($currentMonthAchievement > 0 ? 'Achievement:  '.number_format($currentMonthAchievement, 0, ',', '.') : 'Belum ada pencapaian')
                ->descriptionIcon($monthlyPercentage >= 100 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyPercentage >= 100 ? 'success' : ($monthlyPercentage >= 50 ? 'warning' : 'danger'))
                ->chart([10, 15, 8, 20, 25, 18, 30]),

            Stat::make('Achievement Bulan Ini', $monthlyPercentage.'%')
                ->description($monthlyPercentage >= 100 ? 'Target tercapai!' : 'Dari target bulan ini')
                ->descriptionIcon($monthlyPercentage >= 100 ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                ->color($monthlyPercentage >= 100 ? 'success' : ($monthlyPercentage >= 80 ? 'warning' : 'danger'))
                ->chart([5, 10, 15, 25, 30, 35, $monthlyPercentage]),

            Stat::make('Target Tahunan', ' '.number_format($yearlyTarget, 0, ',', '.'))
                ->description($yearlyAchievement > 0 ? 'Achievement:  '.number_format($yearlyAchievement, 0, ',', '.') : 'Belum ada pencapaian')
                ->descriptionIcon($yearlyPercentage >= 50 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($yearlyPercentage >= 80 ? 'success' : ($yearlyPercentage >= 50 ? 'warning' : 'danger'))
                ->chart([20, 25, 30, 35, 40, 45, $yearlyPercentage]),

            Stat::make('Account Managers', $activeAccountManagers.' Aktif')
                ->description($achievedTargetsThisMonth.' target tercapai bulan ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 20, $activeAccountManagers]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
