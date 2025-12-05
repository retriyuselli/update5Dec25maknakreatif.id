<?php

namespace App\Filament\Resources\Piutangs\Widgets;

use App\Enums\StatusPiutang;
use App\Models\PembayaranPiutang;
use App\Models\Piutang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PiutangOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total Piutang
        $totalPiutang = Piutang::sum('total_piutang');
        $totalSudahDibayar = Piutang::sum('sudah_dibayar');
        $totalSisaPiutang = Piutang::sum('sisa_piutang');

        // Jumlah Piutang per Status
        $piutangAktif = Piutang::where('status', StatusPiutang::AKTIF)->count();
        $piutangLunas = Piutang::where('status', StatusPiutang::LUNAS)->count();
        $piutangJatuhTempo = Piutang::where('status', StatusPiutang::JATUH_TEMPO)->count();

        // Piutang Bulan Ini
        $piutangBulanIni = Piutang::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_piutang');

        // Pembayaran Bulan Ini
        $pembayaranBulanIni = PembayaranPiutang::whereMonth('tanggal_pembayaran', now()->month)
            ->whereYear('tanggal_pembayaran', now()->year)
            ->where('status', 'dikonfirmasi')
            ->sum('total_pembayaran');

        return [
            Stat::make('Total Piutang', 'Rp '.Number::format($totalPiutang, 0))
                ->description('Seluruh piutang yang tercatat')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart([
                    Piutang::whereDate('created_at', '>=', now()->subDays(7))->sum('total_piutang'),
                    Piutang::whereDate('created_at', '>=', now()->subDays(6))->sum('total_piutang'),
                    Piutang::whereDate('created_at', '>=', now()->subDays(5))->sum('total_piutang'),
                    Piutang::whereDate('created_at', '>=', now()->subDays(4))->sum('total_piutang'),
                    Piutang::whereDate('created_at', '>=', now()->subDays(3))->sum('total_piutang'),
                    Piutang::whereDate('created_at', '>=', now()->subDays(2))->sum('total_piutang'),
                    Piutang::whereDate('created_at', '>=', now()->subDays(1))->sum('total_piutang'),
                ]),

            Stat::make('Sisa Piutang', 'Rp '.Number::format($totalSisaPiutang, 0))
                ->description('Yang belum dibayar')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->chart([
                    $totalSisaPiutang - ($totalSisaPiutang * 0.1),
                    $totalSisaPiutang - ($totalSisaPiutang * 0.08),
                    $totalSisaPiutang - ($totalSisaPiutang * 0.05),
                    $totalSisaPiutang - ($totalSisaPiutang * 0.03),
                    $totalSisaPiutang,
                ]),

            Stat::make('Sudah Dibayar', 'Rp '.Number::format($totalSudahDibayar, 0))
                ->description('Total pembayaran diterima')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([
                    $totalSudahDibayar * 0.7,
                    $totalSudahDibayar * 0.8,
                    $totalSudahDibayar * 0.85,
                    $totalSudahDibayar * 0.9,
                    $totalSudahDibayar,
                ]),

            Stat::make('Piutang Aktif', $piutangAktif)
                ->description('Masih dalam periode')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Jatuh Tempo', $piutangJatuhTempo)
                ->description('Perlu tindak lanjut')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),

            Stat::make('Piutang Bulan Ini', 'Rp '.Number::format($piutangBulanIni, 0))
                ->description('Piutang baru di '.now()->format('M Y'))
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),

            Stat::make('Pembayaran Bulan Ini', 'Rp '.Number::format($pembayaranBulanIni, 0))
                ->description('Pembayaran di '.now()->format('M Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Tingkat Pelunasan',
                $totalPiutang > 0 ? number_format(($totalSudahDibayar / $totalPiutang) * 100, 1).'%' : '0%'
            )
                ->description('Persentase pembayaran')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($totalPiutang > 0 && ($totalSudahDibayar / $totalPiutang) > 0.8 ? 'success' : 'warning'),
        ];
    }

    protected static ?int $sort = 1;
}
