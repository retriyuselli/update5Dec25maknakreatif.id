<?php

namespace App\Filament\Widgets;

use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\ExpenseOps;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardKeuangan extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    protected static ?int $sort = 11;


    public function getHeading(): ?string
    {
        return 'Data Finance';
    }

    public function getDescription(): ?string
    {
        return 'Ringkasan data finance bulan berjalan ('.now()->monthName.' '.now()->year.')';
    }

    protected function getStats(): array
    {
        $startFilter = $this->pageFilters['startDate'] ?? null;
        $endFilter = $this->pageFilters['endDate'] ?? null;

        $currentStart = $startFilter ? \Carbon\Carbon::parse($startFilter) : now()->startOfMonth();
        $currentEnd = $endFilter ? \Carbon\Carbon::parse($endFilter) : now()->endOfMonth();

        $totalPaymentCurrentMonth = DataPembayaran::whereBetween('created_at', [$currentStart, $currentEnd])->sum('nominal');
        $totalExpenseCurrentMonth = Expense::whereBetween('created_at', [$currentStart, $currentEnd])->sum('amount');
        $totalExpenseOpsCurrentMonth = ExpenseOps::whereBetween('created_at', [$currentStart, $currentEnd])->sum('amount');

        $totalExpenseAllCurrentMonth = $totalExpenseCurrentMonth + $totalExpenseOpsCurrentMonth;
        $netDifferenceCurrentMonth = $totalPaymentCurrentMonth - $totalExpenseAllCurrentMonth;

        $startOfPreviousMonth = $currentStart->copy()->subMonthNoOverflow();
        $endOfPreviousMonth = $currentEnd->copy()->subMonthNoOverflow();

        $totalPaymentPreviousMonth = DataPembayaran::whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])->sum('nominal');
        $totalExpensePreviousMonth = Expense::whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])->sum('amount');
        $totalExpenseOpsPreviousMonth = ExpenseOps::whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])->sum('amount');

        $totalExpenseAllPreviousMonth = $totalExpensePreviousMonth + $totalExpenseOpsPreviousMonth;
        $netDifferencePreviousMonth = $totalPaymentPreviousMonth - $totalExpenseAllPreviousMonth;

        // Fungsi helper untuk deskripsi perubahan
        $getChangeDescription = function ($current, $previous, $label) {
            // Ensure inputs are numeric by casting to float
            $currentVal = (float) $current;
            $previousVal = (float) $previous;

            if ($previousVal == 0.0) { // Compare with float 0.0
                if ($currentVal > 0.0) {
                    return $label.' meningkat (bulan lalu Rp 0)';
                } elseif ($currentVal < 0.0) {
                    return $label.' menurun (bulan lalu Rp 0)';
                }

                return $label.' tidak berubah (bulan lalu Rp 0)';
            }

            // Calculate percentage change using the numeric values
            // abs($previousVal) is now safe as $previousVal is a float
            $change = (($currentVal - $previousVal) / abs($previousVal)) * 100;

            // Format the absolute change for display
            $formattedPercentage = number_format(abs($change), 1, ',', '.');

            if ($change > 0.0) {
                return $label.' naik '.$formattedPercentage.'% dari bulan lalu';
            } elseif ($change < 0.0) {
                return $label.' turun '.$formattedPercentage.'% dari bulan lalu';
            }

            return $label.' tidak berubah dari bulan lalu';
        };

        return [
            Stat::make('Pembayaran Masuk', ' '.number_format($totalPaymentCurrentMonth, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->description($getChangeDescription($totalPaymentCurrentMonth, $totalPaymentPreviousMonth, 'Pembayaran'))
                ->descriptionIcon($totalPaymentCurrentMonth >= $totalPaymentPreviousMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($totalPaymentCurrentMonth >= $totalPaymentPreviousMonth ? 'success' : 'danger'),

            Stat::make('Pengeluaran Wedding', ' '.number_format($totalExpenseCurrentMonth, 0, ',', '.'))
                ->icon('heroicon-o-credit-card')
                ->description($getChangeDescription($totalExpenseCurrentMonth, $totalExpensePreviousMonth, 'Pengeluaran Wedding'))
                ->descriptionIcon($totalExpenseCurrentMonth <= $totalExpensePreviousMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down') // Icon up jika pengeluaran lebih rendah (baik)
                ->color($totalExpenseCurrentMonth <= $totalExpensePreviousMonth ? 'success' : 'danger'),

            Stat::make('Pengeluaran Operasional', ' '.number_format($totalExpenseOpsCurrentMonth, 0, ',', '.'))
                ->icon('heroicon-o-wrench-screwdriver')
                ->description($getChangeDescription($totalExpenseOpsCurrentMonth, $totalExpenseOpsPreviousMonth, 'Pengeluaran Ops'))
                ->descriptionIcon($totalExpenseOpsCurrentMonth <= $totalExpenseOpsPreviousMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down') // Icon up jika pengeluaran lebih rendah (baik)
                ->color($totalExpenseOpsCurrentMonth <= $totalExpenseOpsPreviousMonth ? 'success' : 'danger'),

            // Stat::make('Selisih (Masuk - Keluar)', ' '.number_format($netDifferenceCurrentMonth, 0, ',', '.'))
            //     ->icon('heroicon-o-chart-bar')
            //     ->description($getChangeDescription($netDifferenceCurrentMonth, $netDifferencePreviousMonth, 'Selisih'))
            //     ->descriptionIcon($netDifferenceCurrentMonth >= $netDifferencePreviousMonth ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            //     ->color($netDifferenceCurrentMonth >= 0 ? 'success' : 'danger'),
        ];
    }
}
