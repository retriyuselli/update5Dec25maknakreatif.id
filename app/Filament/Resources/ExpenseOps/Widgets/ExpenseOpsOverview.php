<?php

namespace App\Filament\Resources\ExpenseOps\Widgets;

use App\Models\ExpenseOps;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpenseOpsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get operational expenses for the current month
        $currentMonthOpsExpenses = ExpenseOps::whereBetween('date_expense', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->sum('amount');

        // Get operational expenses for the previous month
        $previousMonthOpsExpenses = ExpenseOps::whereBetween('date_expense', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ])->sum('amount');

        // Calculate the percentage change
        $changePercentage = 0;
        if ($previousMonthOpsExpenses > 0) {
            $changePercentage = (($currentMonthOpsExpenses - $previousMonthOpsExpenses) / $previousMonthOpsExpenses) * 100;
        } elseif ($currentMonthOpsExpenses > 0) {
            $changePercentage = 100; // Infinite growth from zero
        }

        // Determine the trend icon and color
        $trendIcon = null;
        $trendColor = 'gray';
        if ($changePercentage > 0) {
            $trendIcon = 'heroicon-m-arrow-trending-up';
            $trendColor = 'danger'; // More expenses is usually a negative trend
        } elseif ($changePercentage < 0) {
            $trendIcon = 'heroicon-m-arrow-trending-down';
            $trendColor = 'success'; // Fewer expenses is usually a positive trend
        }

        // Format the change description
        $changeDescription = number_format(abs($changePercentage), 1).'% '.($changePercentage >= 0 ? 'naik' : 'turun');

        // Get total number of operational expenses without an image
        $opsExpensesWithoutImageCount = ExpenseOps::whereNull('image')->orWhere('image', '')->count();

        // Get total amount of operational expenses for the current year
        $currentYearOpsExpenses = ExpenseOps::whereYear('date_expense', Carbon::now()->year)->sum('amount');

        // Get total number of operational expenses for the current year
        $currentYearOpsExpensesCount = ExpenseOps::whereYear('date_expense', Carbon::now()->year)->count();

        return [
            Stat::make('Total Pengeluaran Operasional (Bulan Ini)', ''.number_format($currentMonthOpsExpenses, 0, ',', '.'))
                ->description($changeDescription.' dari bulan lalu')
                ->descriptionIcon($trendIcon, IconPosition::Before)
                ->color($trendColor)
                ->url(route('expense-ops.pdf-report', [
                    'year' => Carbon::now()->year,
                    'month' => Carbon::now()->month,
                ])),

            Stat::make('Total Pengeluaran Operasional (Tahun Ini)', ''.number_format($currentYearOpsExpenses, 0, ',', '.'))
                ->description('Total pengeluaran tahun ini')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('primary'),

            Stat::make('Total Transaksi (Tahun Ini)', $currentYearOpsExpensesCount)
                ->description('Jumlah catatan pengeluaran tahun ini')
                ->descriptionIcon('heroicon-m-receipt-percent', IconPosition::Before)
                ->color('success'),

            Stat::make('Pengeluaran Tanpa Bukti', $opsExpensesWithoutImageCount)
                ->description('Catatan yang memerlukan bukti pembayaran')
                ->descriptionIcon('heroicon-m-exclamation-triangle', IconPosition::Before)
                ->color('warning'),
        ];
    }
}
