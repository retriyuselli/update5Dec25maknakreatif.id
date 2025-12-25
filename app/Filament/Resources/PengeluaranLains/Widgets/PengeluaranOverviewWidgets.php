<?php

namespace App\Filament\Resources\PengeluaranLains\Widgets;

use App\Models\PengeluaranLain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PengeluaranOverviewWidgets extends BaseWidget
{
    protected function getStats(): array
    {
        // Get totals for current year
        $currentYear = Carbon::now()->year;
        $totalPengeluaran = PengeluaranLain::where('kategori_transaksi', 'uang_keluar')
            ->whereYear('date_expense', $currentYear)
            ->sum('amount');
        $totalPemasukan = PengeluaranLain::where('kategori_transaksi', 'uang_masuk')
            ->whereYear('date_expense', $currentYear)
            ->sum('amount');
        $totalTransaksi = PengeluaranLain::whereYear('date_expense', $currentYear)->count();

        // Get monthly data for trend
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthPengeluaran = PengeluaranLain::where('kategori_transaksi', 'uang_keluar')
            ->whereDate('date_expense', '>=', $currentMonth)
            ->sum('amount');

        $lastMonthPengeluaran = PengeluaranLain::where('kategori_transaksi', 'uang_keluar')
            ->whereDate('date_expense', '>=', $lastMonth)
            ->whereDate('date_expense', '<', $currentMonth)
            ->sum('amount');

        $currentMonthTransaksi = PengeluaranLain::whereDate('date_expense', '>=', $currentMonth)->count();
        $lastMonthTransaksi = PengeluaranLain::whereDate('date_expense', '>=', $lastMonth)
            ->whereDate('date_expense', '<', $currentMonth)
            ->count();

        // Calculate percentage change
        $pengeluaranChange = $lastMonthPengeluaran > 0
            ? (($currentMonthPengeluaran - $lastMonthPengeluaran) / $lastMonthPengeluaran) * 100
            : 0;

        $transaksiChange = $lastMonthTransaksi > 0
            ? (($currentMonthTransaksi - $lastMonthTransaksi) / $lastMonthTransaksi) * 100
            : 0;

        return [
            Stat::make('Total Pengeluaran Tahun Ini', ''.number_format($totalPengeluaran, 0, ',', '.'))
                ->description($pengeluaranChange >= 0
                    ? number_format(abs($pengeluaranChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($pengeluaranChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($pengeluaranChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pengeluaranChange >= 0 ? 'danger' : 'success')
                ->chart($this->getPengeluaranChartData()),

            Stat::make('Pengeluaran Bulan Ini', ''.number_format($currentMonthPengeluaran, 0, ',', '.'))
                ->description($pengeluaranChange >= 0
                    ? number_format(abs($pengeluaranChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($pengeluaranChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($pengeluaranChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pengeluaranChange >= 0 ? 'danger' : 'success'),

            Stat::make('Total Transaksi Tahun Ini', number_format($totalTransaksi, 0, ',', '.'))
                ->description($transaksiChange >= 0
                    ? number_format(abs($transaksiChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($transaksiChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($transaksiChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transaksiChange >= 0 ? 'success' : 'danger')
                ->chart($this->getTransaksiChartData()),

            Stat::make('Transaksi Bulan Ini', number_format($currentMonthTransaksi, 0, ',', '.'))
                ->description($transaksiChange >= 0
                    ? number_format(abs($transaksiChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($transaksiChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($transaksiChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transaksiChange >= 0 ? 'success' : 'danger'),
        ];
    }

    private function getPengeluaranChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $amount = PengeluaranLain::where('kategori_transaksi', 'uang_keluar')
                ->whereDate('date_expense', $date)
                ->sum('amount');
            $data[] = (int) $amount;
        }

        return $data;
    }

    private function getTransaksiChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $count = PengeluaranLain::whereDate('date_expense', $date)->count();
            $data[] = $count;
        }

        return $data;
    }
}
