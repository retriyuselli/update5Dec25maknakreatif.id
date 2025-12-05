<?php

namespace App\Filament\Resources\PaymentMethods\Pages;

use App\Filament\Resources\PaymentMethods\PaymentMethodResource;
use Carbon\Carbon;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class PaymentMethod extends ViewRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected string $view = 'filament.resources.payment-method-resource.pages.payment-method';

    public function getTitle(): string
    {
        return 'Detail Rekening: '.$this->record->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            '/admin/payment-methods' => 'Daftar Rekening',
            '#' => 'Detail: '.$this->record->name,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detail Rekening')
                    ->tabs([
                        Tab::make('Uang Masuk')
                            ->icon('heroicon-o-arrow-down-circle')
                            ->badge(fn ($record) => $this->getTotalIncomeTransactions($record))
                            ->badgeColor('success')
                            ->schema([
                                View::make('filament.resources.payment-method-resource.pages.uang-masuk-detail')
                                    ->viewData(fn ($record) => [
                                        'record' => $record,
                                        'pendapatanLain' => $this->getPendapatanLain($record),
                                        'dataPembayaran' => $this->getDataPembayaran($record),
                                        'totalUangMasuk' => $record->getTotalUangMasuk(),
                                    ]),
                            ]),

                        Tab::make('Uang Keluar')
                            ->icon('heroicon-o-arrow-up-circle')
                            ->badge(fn ($record) => $this->getTotalExpenseTransactions($record))
                            ->badgeColor('danger')
                            ->schema([
                                View::make('filament.resources.payment-method-resource.pages.uang-keluar-detail')
                                    ->viewData(fn ($record) => [
                                        'record' => $record,
                                        'expenses' => $this->getExpenses($record),
                                        'expenseOps' => $this->getExpenseOps($record),
                                        'pengeluaranLain' => $this->getPengeluaranLain($record),
                                        'totalUangKeluar' => $record->getTotalUangKeluar(),
                                    ]),
                            ]),

                        Tab::make('Laporan Keuangan')
                            ->icon('heroicon-o-chart-bar')
                            // ->badge(fn ($record) => '📊')
                            ->badgeColor('info')
                            ->schema([
                                View::make('filament.resources.payment-method-resource.pages.laporan-keuangan')
                                    ->viewData(fn ($record) => [
                                        'record' => $record,
                                        'breakdown' => $record->getSaldoBreakdown(),
                                        'monthlyData' => $this->getMonthlyFinancialData($record),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        Notification::make()
            ->title('Detail Rekening Dimuat')
            ->body('Gunakan tab di bawah untuk melihat detail Uang Masuk, Uang Keluar, dan Laporan Keuangan.')
            ->info()
            ->duration(3000)
            ->send();
    }

    private function getPendapatanLain($record)
    {
        return $record->pendapatanLains()
            ->when($record->opening_balance_date, function ($query) use ($record) {
                $date = $this->parseDate($record->opening_balance_date);

                return $date ? $query->where('tgl_bayar', '>=', $date) : $query;
            })
            ->whereNull('deleted_at')
            ->orderBy('tgl_bayar', 'desc')
            ->get();
    }

    private function getDataPembayaran($record)
    {
        return $record->payments()
            ->when($record->opening_balance_date, function ($query) use ($record) {
                $date = $this->parseDate($record->opening_balance_date);

                return $date ? $query->where('tgl_bayar', '>=', $date) : $query;
            })
            ->whereNull('deleted_at')
            ->with('order')
            ->orderBy('tgl_bayar', 'desc')
            ->get();
    }

    private function getExpenses($record)
    {
        return $record->expenses()
            ->when($record->opening_balance_date, function ($query) use ($record) {
                $date = $this->parseDate($record->opening_balance_date);

                return $date ? $query->where('date_expense', '>=', $date) : $query;
            })
            ->whereNull('deleted_at')
            ->orderBy('date_expense', 'desc')
            ->get();
    }

    private function getExpenseOps($record)
    {
        return $record->expenseOps()
            ->when($record->opening_balance_date, function ($query) use ($record) {
                $date = $this->parseDate($record->opening_balance_date);

                return $date ? $query->where('date_expense', '>=', $date) : $query;
            })
            ->whereNull('deleted_at')
            ->orderBy('date_expense', 'desc')
            ->get();
    }

    private function getPengeluaranLain($record)
    {
        return $record->pengeluaranLains()
            ->when($record->opening_balance_date, function ($query) use ($record) {
                $date = $this->parseDate($record->opening_balance_date);

                return $date ? $query->where('date_expense', '>=', $date) : $query;
            })
            ->whereNull('deleted_at')
            ->orderBy('date_expense', 'desc')
            ->get();
    }

    private function getTotalIncomeTransactions($record): int
    {
        return $this->getPendapatanLain($record)->count() + $this->getDataPembayaran($record)->count();
    }

    private function getTotalExpenseTransactions($record): int
    {
        return $this->getExpenses($record)->count() + $this->getExpenseOps($record)->count() + $this->getPengeluaranLain($record)->count();
    }

    private function getMonthlyFinancialData($record): array
    {
        $startDate = $this->parseDate($record->opening_balance_date) ?? now()->subYear();
        $monthlyData = [];

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $startDate->copy()->addMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $income = $record->payments()
                ->whereBetween('tgl_bayar', [$monthStart, $monthEnd])
                ->whereNull('deleted_at')
                ->sum('nominal') +
                $record->pendapatanLains()
                    ->whereBetween('tgl_bayar', [$monthStart, $monthEnd])
                    ->whereNull('deleted_at')
                    ->sum('nominal');

            $expense = $record->expenses()
                ->whereBetween('date_expense', [$monthStart, $monthEnd])
                ->whereNull('deleted_at')
                ->sum('amount') +
                $record->expenseOps()
                    ->whereBetween('date_expense', [$monthStart, $monthEnd])
                    ->whereNull('deleted_at')
                    ->sum('amount') +
                $record->pengeluaranLains()
                    ->whereBetween('date_expense', [$monthStart, $monthEnd])
                    ->whereNull('deleted_at')
                    ->sum('amount');

            $monthlyData[] = [
                'month' => $monthStart->format('M Y'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        }

        return $monthlyData;
    }

    /**
     * Helper method untuk mengkonversi tanggal menjadi Carbon instance
     */
    private function parseDate($date)
    {
        if (! $date) {
            return null;
        }

        try {
            return is_string($date) ? Carbon::parse($date) : $date;
        } catch (Exception $e) {
            Notification::make()
                ->title('Error Format Tanggal')
                ->body('Terjadi kesalahan dalam memformat tanggal: '.$e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }
}
