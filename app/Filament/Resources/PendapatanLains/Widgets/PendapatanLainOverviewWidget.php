<?php

namespace App\Filament\Resources\PendapatanLains\Widgets;

use App\Models\PendapatanLain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PendapatanLainOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get totals for current year
        $currentYear = Carbon::now()->year;
        $totalPendapatan = PendapatanLain::where('kategori_transaksi', 'uang_masuk')
            ->whereYear('tgl_bayar', $currentYear)
            ->sum('nominal');
        $totalPengeluaran = PendapatanLain::where('kategori_transaksi', 'uang_keluar')
            ->whereYear('tgl_bayar', $currentYear)
            ->sum('nominal');
        $totalTransaksi = PendapatanLain::whereYear('tgl_bayar', $currentYear)->count();

        // Get monthly data for trend
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthPendapatan = PendapatanLain::where('kategori_transaksi', 'uang_masuk')
            ->whereDate('tgl_bayar', '>=', $currentMonth)
            ->sum('nominal');

        $lastMonthPendapatan = PendapatanLain::where('kategori_transaksi', 'uang_masuk')
            ->whereDate('tgl_bayar', '>=', $lastMonth)
            ->whereDate('tgl_bayar', '<', $currentMonth)
            ->sum('nominal');

        $currentMonthTransaksi = PendapatanLain::whereDate('tgl_bayar', '>=', $currentMonth)->count();
        $lastMonthTransaksi = PendapatanLain::whereDate('tgl_bayar', '>=', $lastMonth)
            ->whereDate('tgl_bayar', '<', $currentMonth)
            ->count();

        // Calculate percentage change
        $pendapatanChange = $lastMonthPendapatan > 0
            ? (($currentMonthPendapatan - $lastMonthPendapatan) / $lastMonthPendapatan) * 100
            : 0;

        $transaksiChange = $lastMonthTransaksi > 0
            ? (($currentMonthTransaksi - $lastMonthTransaksi) / $lastMonthTransaksi) * 100
            : 0;

        // Calculate net profit (pendapatan - pengeluaran)
        $netProfit = $totalPendapatan - $totalPengeluaran;

        return [
            Stat::make('Pendapatan Tahun Ini', ''.number_format($totalPendapatan, 0, ',', '.'))
                ->description($pendapatanChange >= 0
                    ? number_format(abs($pendapatanChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($pendapatanChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($pendapatanChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pendapatanChange >= 0 ? 'success' : 'danger')
                ->chart($this->getPendapatanChartData()),

            Stat::make('Pendapatan Bulan Ini', ''.number_format($currentMonthPendapatan, 0, ',', '.'))
                ->description($pendapatanChange >= 0
                    ? number_format(abs($pendapatanChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($pendapatanChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($pendapatanChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pendapatanChange >= 0 ? 'success' : 'danger'),

            Stat::make('Net Profit', ''.number_format($netProfit, 0, ',', '.'))
                ->description('Pendapatan bersih (Income - Expense)')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Total Transaksi', number_format($totalTransaksi, 0, ',', '.'))
                ->description($transaksiChange >= 0
                    ? number_format(abs($transaksiChange), 1).'% naik dari bulan lalu'
                    : number_format(abs($transaksiChange), 1).'% turun dari bulan lalu'
                )
                ->descriptionIcon($transaksiChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transaksiChange >= 0 ? 'success' : 'danger')
                ->chart($this->getTransaksiChartData()),
        ];
    }

    private function getPendapatanChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $amount = PendapatanLain::where('kategori_transaksi', 'uang_masuk')
                ->whereDate('tgl_bayar', $date)
                ->sum('nominal');
            $data[] = (int) $amount;
        }

        return $data;
    }

    private function getTransaksiChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $count = PendapatanLain::whereDate('tgl_bayar', $date)->count();
            $data[] = $count;
        }

        return $data;
    }
}
