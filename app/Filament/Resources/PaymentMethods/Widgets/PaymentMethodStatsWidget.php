<?php

namespace App\Filament\Resources\PaymentMethods\Widgets;

use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\ExpenseOps;
use App\Models\PaymentMethod;
use App\Models\PendapatanLain;
use App\Models\PengeluaranLain;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PaymentMethodStatsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected array|int|null $columns = ['default' => 1, 'md' => 2];

    // Define the filter property
    protected array $pageFilters = [
        'year' => null,
        'month' => null,
        'show_details' => false,
    ];

    public function mount()
    {
        // Set default values if not already set
        if (! isset($this->pageFilters['year'])) {
            $this->pageFilters['year'] = date('Y');
        }
        if (! isset($this->pageFilters['month'])) {
            $this->pageFilters['month'] = date('m');
        }
        if (! isset($this->pageFilters['show_details'])) {
            $this->pageFilters['show_details'] = false;
        }

        // Ensure month is properly formatted
        $this->pageFilters['month'] = str_pad($this->pageFilters['month'], 2, '0', STR_PAD_LEFT);
    }

    protected function validateFilters(): void
    {
        // Ensure year is valid
        if (! is_numeric($this->pageFilters['year'])) {
            $this->pageFilters['year'] = date('Y');
        }

        // Ensure month is valid (01-12)
        $month = $this->pageFilters['month'];
        if (! is_numeric($month) || $month < 1 || $month > 12) {
            $this->pageFilters['month'] = date('m');
        }
        $this->pageFilters['month'] = str_pad($this->pageFilters['month'], 2, '0', STR_PAD_LEFT);

        // Ensure show_details is boolean
        $this->pageFilters['show_details'] = (bool) ($this->pageFilters['show_details'] ?? false);
    }

    protected function getFilters(): ?array
    {
        // Get available years from all transaction tables
        $years = collect();

        // Get years from DataPembayaran
        $paymentYears = DataPembayaran::select(DB::raw('YEAR(tgl_bayar) as year'))
            ->distinct()
            ->whereNotNull('tgl_bayar')
            ->pluck('year');

        // Get years from PendapatanLain
        $incomeYears = PendapatanLain::select(DB::raw('YEAR(tgl_bayar) as year'))
            ->distinct()
            ->whereNotNull('tgl_bayar')
            ->pluck('year');

        // Get years from all expense tables
        $expenseYears = Expense::select(DB::raw('YEAR(date_expense) as year'))
            ->distinct()
            ->whereNotNull('date_expense')
            ->pluck('year');

        $expenseOpsYears = ExpenseOps::select(DB::raw('YEAR(date_expense) as year'))
            ->distinct()
            ->whereNotNull('date_expense')
            ->pluck('year');

        $pengeluaranYears = PengeluaranLain::select(DB::raw('YEAR(date_expense) as year'))
            ->distinct()
            ->whereNotNull('date_expense')
            ->pluck('year');

        $years = $years->merge($paymentYears)
            ->merge($incomeYears)
            ->merge($expenseYears)
            ->merge($expenseOpsYears)
            ->merge($pengeluaranYears)
            ->unique()
            ->sort()
            ->reverse();

        return [
            'year' => Select::make('year')
                ->label('Tahun')
                ->options($years->mapWithKeys(fn ($year) => [$year => $year])->toArray())
                ->default(date('Y')),
            'month' => Select::make('month')
                ->label('Bulan')
                ->options([
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember',
                ])
                ->default(date('m')),
            'show_details' => Toggle::make('show_details')
                ->label('Tampilkan Detail Breakdown')
                ->default(false),
        ];
    }

    protected function getStats(): array
    {
        // Validate filters before using them
        $this->validateFilters();

        $year = $this->pageFilters['year'];
        $month = $this->pageFilters['month'];
        $showDetails = $this->pageFilters['show_details'];

        $paymentMethods = PaymentMethod::with([
            'payments' => fn ($q) => $q->whereNull('deleted_at'),
            'pendapatanLains' => fn ($q) => $q->whereNull('deleted_at'),
            'expenses' => fn ($q) => $q->whereNull('deleted_at'),
            'expenseOps' => fn ($q) => $q->whereNull('deleted_at'),
            'pengeluaranLains' => fn ($q) => $q->whereNull('deleted_at'),
        ])->get();

        // Pre-calculate all saldos to prevent repeated calculations
        $paymentMethods->each(function ($method) {
            $method->getSaldoAttribute(); // This will cache the saldo
        });

        $stats = [];

        // Cache saldo calculations
        $methodSaldos = $paymentMethods->mapWithKeys(function ($method) {
            return [$method->id => $method->saldo];
        });

        // Perhitungan summary langsung dari seluruh tabel
        $totalMasukSemua = (
            DataPembayaran::whereYear('tgl_bayar', $year)
                ->whereMonth('tgl_bayar', $month)
                ->whereNull('deleted_at')
                ->sum('nominal')
        ) + (
            PendapatanLain::whereYear('tgl_bayar', $year)
                ->whereMonth('tgl_bayar', $month)
                ->whereNull('deleted_at')
                ->sum('nominal')
        );

        $totalKeluarSemua = (
            Expense::whereYear('date_expense', $year)
                ->whereMonth('date_expense', $month)
                ->whereNull('deleted_at')
                ->sum('amount')
        ) + (
            ExpenseOps::whereYear('date_expense', $year)
                ->whereMonth('date_expense', $month)
                ->whereNull('deleted_at')
                ->sum('amount')
        ) + (
            PengeluaranLain::whereYear('date_expense', $year)
                ->whereMonth('date_expense', $month)
                ->whereNull('deleted_at')
                ->sum('amount')
        );

        $totalSaldoSemua = $methodSaldos->sum();

        $stats = [];
        foreach ($paymentMethods as $method) {
            $currentSaldo = $methodSaldos[$method->id];
            $periodMasuk = $this->calculatePeriodIncome($method, $year, $month);
            $periodKeluar = $this->calculatePeriodExpense($method, $year, $month);
            $netFlow = $periodMasuk - $periodKeluar;
            $formattedSaldo = ' '.number_format($currentSaldo, 0, ',', '.');
            $formattedNetFlow = 'Rp '.number_format($netFlow, 0, ',', '.');
            $color = $currentSaldo >= 0 ? 'success' : 'danger';
            $icon = $currentSaldo >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
            $flowColor = $netFlow >= 0 ? 'success' : 'danger';
            $flowIcon = $netFlow >= 0 ? '📈' : '📉';

            if ($showDetails) {
                $description = sprintf(
                    "%s Periode %s/%s\nMasuk: Rp %s\nKeluar: Rp %s\nNo.Rek: %s",
                    $flowIcon,
                    str_pad($month, 2, '0', STR_PAD_LEFT),
                    $year,
                    number_format($periodMasuk, 0, ',', '.'),
                    number_format($periodKeluar, 0, ',', '.'),
                    $method->no_rekening
                );
            } else {
                $description = sprintf(
                    '%s %s periode %s/%s | No.Rek: %s',
                    $flowIcon,
                    $formattedNetFlow,
                    str_pad($month, 2, '0', STR_PAD_LEFT),
                    $year,
                    $method->no_rekening
                );
            }

            $stats[] = Stat::make(
                label: $method->name.' ('.$method->bank_name.')',
                value: $formattedSaldo
            )
                ->description($description)
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($this->generateTrendChart($method, $year));
        }

        $summaryNetFlow = $totalMasukSemua - $totalKeluarSemua;
        $summaryDescription = sprintf(
            'Total periode %s/%s: Masuk Rp %s, Keluar Rp %s',
            str_pad($month, 2, '0', STR_PAD_LEFT),
            $year,
            number_format($totalMasukSemua, 0, ',', '.'),
            number_format($totalKeluarSemua, 0, ',', '.')
        );

        array_unshift($stats, Stat::make(
            label: 'Total Saldo Semua Rekening',
            value: ' '.number_format($totalSaldoSemua, 0, ',', '.')
        )
            ->description($summaryDescription)
            ->descriptionIcon($summaryNetFlow >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
            ->color($totalSaldoSemua >= 0 ? 'success' : 'danger')
        );

        return $stats;
    }

    private function calculatePeriodIncome(PaymentMethod $method, $year, $month): float
    {
        // Income from DataPembayaran (wedding payments)
        $weddingIncome = $method->payments()
            ->whereYear('tgl_bayar', $year)
            ->whereMonth('tgl_bayar', $month)
            ->whereNull('deleted_at')
            ->sum('nominal') ?? 0;

        // Income from PendapatanLain (other income)
        $otherIncome = $method->pendapatanLains()
            ->whereYear('tgl_bayar', $year)
            ->whereMonth('tgl_bayar', $month)
            ->whereNull('deleted_at')
            ->sum('nominal') ?? 0;

        return (float) ($weddingIncome + $otherIncome);
    }

    /**
     * Calculate expenses for specific period
     */
    private function calculatePeriodExpense(PaymentMethod $method, $year, $month): float
    {
        // Expenses from Expense (wedding expenses)
        $weddingExpense = $method->expenses()
            ->whereYear('date_expense', $year)
            ->whereMonth('date_expense', $month)
            ->whereNull('deleted_at')
            ->sum('amount') ?? 0;

        // Expenses from ExpenseOps (operational expenses)
        $operationalExpense = $method->expenseOps()
            ->whereYear('date_expense', $year)
            ->whereMonth('date_expense', $month)
            ->whereNull('deleted_at')
            ->sum('amount') ?? 0;

        // Expenses from PengeluaranLain (other expenses)
        $otherExpense = $method->pengeluaranLains()
            ->whereYear('date_expense', $year)
            ->whereMonth('date_expense', $month)
            ->whereNull('deleted_at')
            ->sum('amount') ?? 0;

        return (float) ($weddingExpense + $operationalExpense + $otherExpense);
    }

    /**
     * Generate trend chart for the past 6 months
     */
    private function generateTrendChart(PaymentMethod $method, $currentYear): array
    {
        $chart = [];
        $currentMonth = (int) date('m');

        for ($i = 5; $i >= 0; $i--) {
            $month = $currentMonth - $i;
            $year = $currentYear;

            if ($month <= 0) {
                $month += 12;
                $year--;
            }

            $income = $this->calculatePeriodIncome($method, $year, $month);
            $expense = $this->calculatePeriodExpense($method, $year, $month);
            $netFlow = $income - $expense;

            $chart[] = $netFlow;
        }

        return $chart;
    }

    /**
     * Public method for testing
     */
    public function testCalculations($paymentMethodId, $year, $month): array
    {
        $method = PaymentMethod::find($paymentMethodId);
        if (! $method) {
            return ['error' => 'Payment method not found'];
        }

        $income = $this->calculatePeriodIncome($method, $year, $month);
        $expense = $this->calculatePeriodExpense($method, $year, $month);
        $netFlow = $income - $expense;
        $currentSaldo = $method->saldo;

        return [
            'payment_method' => $method->name,
            'period' => sprintf('%02d/%s', $month, $year),
            'period_income' => $income,
            'period_expense' => $expense,
            'net_flow' => $netFlow,
            'current_saldo' => $currentSaldo,
            'formatted' => [
                'income' => 'Rp '.number_format($income, 0, ',', '.'),
                'expense' => 'Rp '.number_format($expense, 0, ',', '.'),
                'net_flow' => 'Rp '.number_format($netFlow, 0, ',', '.'),
                'saldo' => 'Rp '.number_format($currentSaldo, 0, ',', '.'),
            ],
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->button()
                ->action(function () {
                    // Generate data for export (e.g., from your stats calculations)
                    $exportData = $this->getExportData();

                    // Trigger the Excel download
                    return Excel::download(new PaymentMethodStatsExport($exportData), 'payment_method_stats.xlsx');
                }),
        ];
    }
}
