<?php

namespace App\Filament\Resources\BankStatements\Widgets;

use App\Models\BankStatement;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class BankStatementOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil data bulan berjalan
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Ambil bulan sebelumnya untuk perbandingan
        $previousMonth = $currentMonth->copy()->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();

        // Statistik bulan berjalan
        $currentMonthStatements = BankStatement::whereBetween('period_start', [$startOfMonth, $endOfMonth])->get();
        $previousMonthStatements = BankStatement::whereBetween('period_start', [$startOfPreviousMonth, $endOfPreviousMonth])->get();

        // Hitung total
        $totalStatements = BankStatement::count();
        $totalCurrentCredit = $currentMonthStatements->sum('tot_credit') ?? 0;
        $totalCurrentDebit = $currentMonthStatements->sum('tot_debit') ?? 0;
        $totalPreviousCredit = $previousMonthStatements->sum('tot_credit') ?? 0;
        $totalPreviousDebit = $previousMonthStatements->sum('tot_debit') ?? 0;

        // Perhitungan arus kas bersih
        $currentNetFlow = $totalCurrentCredit - $totalCurrentDebit;
        $previousNetFlow = $totalPreviousCredit - $totalPreviousDebit;

        // Hitung perubahan
        $creditChange = $totalPreviousCredit > 0
            ? (($totalCurrentCredit - $totalPreviousCredit) / $totalPreviousCredit) * 100
            : ($totalCurrentCredit > 0 ? 100 : 0);

        $debitChange = $totalPreviousDebit > 0
            ? (($totalCurrentDebit - $totalPreviousDebit) / $totalPreviousDebit) * 100
            : ($totalCurrentDebit > 0 ? 100 : 0);

        $netFlowChange = $previousNetFlow != 0
            ? (($currentNetFlow - $previousNetFlow) / abs($previousNetFlow)) * 100
            : ($currentNetFlow > 0 ? 100 : ($currentNetFlow < 0 ? -100 : 0));

        // Distribusi status
        $statusCounts = BankStatement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $parsedCount = $statusCounts['parsed'] ?? 0;
        $totalCount = array_sum($statusCounts);
        $parsedPercentage = $totalCount > 0 ? ($parsedCount / $totalCount) * 100 : 0;

        // Perhitungan rata-rata saldo
        $latestStatements = BankStatement::latest('period_end')->take(5)->get();
        $avgClosingBalance = $latestStatements->avg('closing_balance') ?? 0;

        return [
            // Total Credit (Uang Masuk)
            Stat::make('Total Uang Masuk', 'Rp '.Number::format($totalCurrentCredit, 0))
                ->description($this->getChangeDescription($creditChange, 'dibanding bulan lalu'))
                ->descriptionIcon($creditChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($creditChange >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyTrendData($currentMonthStatements, 'tot_credit')),

            // Total Debit (Uang Keluar)
            Stat::make('Total Uang Keluar', 'Rp '.Number::format($totalCurrentDebit, 0))
                ->description($this->getChangeDescription($debitChange, 'dibanding bulan lalu'))
                ->descriptionIcon($debitChange <= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($debitChange <= 0 ? 'success' : 'warning')
                ->chart($this->getMonthlyTrendData($currentMonthStatements, 'tot_debit')),

            // Arus Kas Bersih
            Stat::make('Arus Kas Bersih', 'Rp '.Number::format($currentNetFlow, 0))
                ->description($this->getChangeDescription($netFlowChange, 'dibanding bulan lalu'))
                ->descriptionIcon($netFlowChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($currentNetFlow >= 0 ? 'success' : 'danger')
                ->chart($this->getNetFlowTrendData()),

            // Rata-rata Saldo
            Stat::make('Rata-rata Saldo', 'Rp '.Number::format($avgClosingBalance, 0))
                ->description('Berdasarkan 5 rekening koran terakhir')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            // Total Rekening Koran
            Stat::make('Total Rekening Koran', Number::format($totalStatements, 0))
                ->description($parsedPercentage > 0 ? number_format($parsedPercentage, 1).'% telah diproses' : 'Belum ada yang diproses')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            // Rekening Aktif
            Stat::make('Rekening Aktif', Number::format(PaymentMethod::count(), 0))
                ->description('Total metode pembayaran terdaftar')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('gray'),
        ];
    }

    private function getChangeDescription(float $percentage, string $suffix): string
    {
        $absPercentage = abs($percentage);
        $direction = $percentage >= 0 ? '+' : '';

        if ($absPercentage < 0.1) {
            return 'Tidak ada perubahan '.$suffix;
        }

        return $direction.number_format($percentage, 1).'% '.$suffix;
    }

    private function getMonthlyTrendData($statements, string $field): array
    {
        if ($statements->isEmpty()) {
            return [0, 0, 0, 0, 0, 0, 0];
        }

        // Kelompokkan berdasarkan minggu dalam bulan berjalan
        $weeklyData = [];
        $statements->each(function ($statement) use (&$weeklyData, $field) {
            $week = Carbon::parse($statement->period_start)->week;
            $weeklyData[$week] = ($weeklyData[$week] ?? 0) + ($statement->$field ?? 0);
        });

        // Konversi ke array 7 titik data untuk grafik
        $chartData = array_values($weeklyData);

        // Tambahkan atau potong hingga 7 titik
        while (count($chartData) < 7) {
            $chartData[] = end($chartData) ?: 0;
        }

        return array_slice($chartData, 0, 7);
    }

    private function getNetFlowTrendData(): array
    {
        // Ambil data 7 hari terakhir untuk tren arus kas bersih
        $statements = BankStatement::where('period_start', '>=', Carbon::now()->subDays(7))
            ->orderBy('period_start')
            ->get();

        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStatements = $statements->filter(function ($statement) use ($date) {
                return Carbon::parse($statement->period_start)->isSameDay($date);
            });

            $netFlow = $dayStatements->sum('tot_credit') - $dayStatements->sum('tot_debit');
            $trendData[] = $netFlow;
        }

        return $trendData;
    }

    protected function getColumns(): int
    {
        return 3; // Tampilkan statistik dalam 3 kolom untuk tata letak yang lebih baik
    }

    public function getDisplayName(): string
    {
        return 'Ringkasan Rekening Koran';
    }
}
