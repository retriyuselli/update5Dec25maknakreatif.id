<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\ExpenseOps;
use App\Models\Order;
use App\Models\Prospect;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 12;

    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $defaultStart = Carbon::now()->startOfMonth()->toDateString();
        $defaultEnd = Carbon::now()->endOfMonth()->toDateString();

        $start = $this->pageFilters['startDate'] ?? $defaultStart;
        $end = $this->pageFilters['endDate'] ?? $defaultEnd;

        // Calculate current period metrics
        $totalRevenue = $this->calculateTotalRevenue($start, $end);
        $totalReceived = $this->calculateTotalReceived($start, $end);
        $totalOutstanding = $totalRevenue - $totalReceived;
        $totalExpenses = $this->calculateTotalExpenses($start, $end);
        $totalProfit = $totalRevenue - $totalExpenses;

        // Calculate previous period metrics for comparison
        $previousStart = $start ? Carbon::parse($start)->subMonth() : Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = $end ? Carbon::parse($end)->subMonth() : Carbon::now()->subMonth()->endOfMonth();

        $previousRevenue = $this->calculateTotalRevenue($previousStart, $previousEnd);
        $previousReceived = $this->calculateTotalReceived($previousStart, $previousEnd);
        $previousExpenses = $this->calculateTotalExpenses($previousStart, $previousEnd);
        $previousProfit = $previousRevenue - $previousExpenses;

        // Calculate percentage changes
        $revenueChange = $this->calculatePercentageChange($previousRevenue, $totalRevenue);
        $receivedChange = $this->calculatePercentageChange($previousReceived, $totalReceived);
        $expensesChange = $this->calculatePercentageChange($previousExpenses, $totalExpenses);
        $profitChange = $this->calculatePercentageChange($previousProfit, $totalProfit);

        // Calculate prospect and employee changes
        $currentProspects = $this->calculateNewProspects($start, $end);
        $previousProspects = $this->calculateNewProspects($previousStart, $previousEnd);
        $prospectChange = $this->calculatePercentageChange($previousProspects, $currentProspects);

        $currentEmployees = $this->calculateNewEmployees($start, $end);
        $previousEmployees = $this->calculateNewEmployees($previousStart, $previousEnd);
        $employeeChange = $this->calculatePercentageChange($previousEmployees, $currentEmployees);

        $currentOrders = $this->calculateNewOrders($start, $end);
        $previousOrders = $this->calculateNewOrders($previousStart, $previousEnd);
        $orderChange = $this->calculatePercentageChange($previousOrders, $currentOrders);

        return [
            Stat::make('Total Expenses Ops (Rp)', ''.number_format($totalExpenses))
                ->icon('heroicon-o-currency-dollar')
                ->color($expensesChange >= 0 ? 'success' : 'danger')
                ->description($this->formatChangeDescription($expensesChange))
                ->descriptionIcon($this->getChangeIcon($expensesChange), IconPosition::Before),

            Stat::make('New Orders', $currentOrders)
                ->icon('heroicon-o-shopping-bag')
                ->color($orderChange >= 0 ? 'success' : 'danger')
                ->description($this->formatChangeDescription($orderChange))
                ->descriptionIcon($this->getChangeIcon($orderChange), IconPosition::Before),

            Stat::make('Total Revenue (Rp)', ''.number_format($totalRevenue))
                ->icon('heroicon-o-currency-dollar')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->description($this->formatChangeDescription($revenueChange))
                ->descriptionIcon($this->getChangeIcon($revenueChange), IconPosition::Before),

            Stat::make('Total Received (Rp)', ''.number_format($totalReceived))
                ->icon('heroicon-o-banknotes')
                ->color($receivedChange >= 0 ? 'success' : 'danger')
                ->description($this->formatChangeDescription($receivedChange))
                ->descriptionIcon($this->getChangeIcon($receivedChange), IconPosition::Before),

            Stat::make('Total Outstanding (Rp)', ''.number_format($totalOutstanding))
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->description('Pending payments')
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before),

            Stat::make('Total Profit (Rp)', ''.number_format($totalProfit))
                ->icon('heroicon-o-chart-bar')
                ->color($profitChange >= 0 ? 'success' : 'danger')
                ->description($this->formatChangeDescription($profitChange))
                ->descriptionIcon($this->getChangeIcon($profitChange), IconPosition::Before),
        ];
    }

    private function calculatePercentageChange(int $previous, int $current): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    private function formatChangeDescription(float $change): string
    {
        $prefix = $change >= 0 ? '+' : '';

        return "{$prefix}{$change}% from previous period";
    }

    private function getChangeIcon(float $change): string
    {
        return $change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    }

    private function calculateNewProspects(?string $start = null, ?string $end = null): int
    {
        return Prospect::when($start, fn ($query) => $query->whereDate('created_at', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('created_at', '<=', $end))
            ->count();
    }

    private function calculateNewEmployees(?string $start = null, ?string $end = null): int
    {
        return Employee::when($start, fn ($query) => $query->whereDate('created_at', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('created_at', '<=', $end))
            ->count();
    }

    private function calculateNewOrders(?string $start = null, ?string $end = null): int
    {
        return Order::when($start, fn ($query) => $query->whereDate('closing_date', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('closing_date', '<=', $end))
            ->count();
    }

    private function calculateTotalRevenue(?string $start = null, ?string $end = null): int
    {
        return Order::when($start, fn ($query) => $query->whereDate('closing_date', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('closing_date', '<=', $end))
            ->selectRaw('SUM(total_price + penambahan - pengurangan - promo) as total_revenue')
            ->value('total_revenue') ?? 0;
    }

    private function calculateTotalReceived(?string $start = null, ?string $end = null): int
    {
        return Order::when($start, fn ($query) => $query->whereDate('closing_date', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('closing_date', '<=', $end))
            ->join('data_pembayarans', 'orders.id', '=', 'data_pembayarans.order_id')
            ->sum('data_pembayarans.nominal') ?? 0;
    }

    private function calculateTotalExpenses(?string $start = null, ?string $end = null): int
    {
        return ExpenseOps::when($start, fn ($query) => $query->whereDate('date_expense', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('date_expense', '<=', $end))
            ->sum('amount') ?? 0;
    }

    private function calculateMonthlyRevenue(): int
    {
        return Order::whereMonth('closing_date', now()->month)
            ->whereYear('closing_date', now()->year)
            ->selectRaw('SUM(total_price + penambahan - pengurangan - promo) as total_revenue')
            ->value('total_revenue') ?? 0;
    }

    private function calculateMonthlyExpenses(): int
    {
        return Order::with('items.product')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get()
            ->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return $item->product->cost_price * $item->quantity;
                });
            });
    }

    private function getRevenueChartData(): array
    {
        return Order::select(
            DB::raw('DATE(closing_date) as date'),
            DB::raw('SUM(total_price + penambahan - pengurangan - promo) as total')
        )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->pluck('total', 'date')
            ->toArray();
    }

    private function getProfitChartData(): array
    {
        return Order::with('items.product')
            ->select('created_at')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            })
            ->map(function ($orders) {
                $revenue = $orders->sum(function ($order) {
                    return $order->total_price + $order->penambahan - $order->pengurangan - $order->promo;
                });

                $expenses = $orders->sum(function ($order) {
                    return $order->items->sum(function ($item) {
                        return $item->product->cost_price * $item->quantity;
                    });
                });

                return $revenue - $expenses;
            })
            ->toArray();
    }
}
