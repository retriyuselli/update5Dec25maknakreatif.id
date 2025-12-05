<?php

namespace App\Filament\Widgets;

use App\Models\Prospect;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProspectStatsWidget extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        $start = $this->pageFilters['startDate'] ?? null;
        $end = $this->pageFilters['endDate'] ?? null;

        $startDate = $start ? \Carbon\Carbon::parse($start) : now()->startOfMonth();
        $endDate = $end ? \Carbon\Carbon::parse($end) : now()->endOfMonth();

        $baseQuery = Prospect::query()
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate));

        $totalProspects = (clone $baseQuery)->count();

        $prospectsWithOrders = (clone $baseQuery)
            ->whereHas('orders', function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn ($qq) => $qq->whereDate('closing_date', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('closing_date', '<=', $endDate));
            })
            ->count();

        $prospectsWithoutOrders = (clone $baseQuery)
            ->whereDoesntHave('orders', function ($q) use ($startDate, $endDate) {
                $q->when($startDate, fn ($qq) => $qq->whereDate('closing_date', '>=', $startDate))
                    ->when($endDate, fn ($qq) => $qq->whereDate('closing_date', '<=', $endDate));
            })
            ->count();
        $protectedProspects = $prospectsWithOrders; // Prospects that cannot be deleted
        $conversionRate = $totalProspects > 0 ? round(($prospectsWithOrders / $totalProspects) * 100, 1) : 0;

        return [
            Stat::make('Total Prospect', $totalProspects)
                ->description('Semua prospect yang terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Warm Prospect', $prospectsWithoutOrders)
                ->description('Prospect yang belum dikonversi (dapat dihapus)')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),

            Stat::make('Converted Prospect', $prospectsWithOrders)
                ->description('Prospect yang sudah ada order (dilindungi)')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),

            // Stat::make('Conversion Rate', $conversionRate.'%')
            //     ->description('Tingkat konversi prospect ke order')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->color($conversionRate >= 50 ? 'success' : ($conversionRate >= 25 ? 'warning' : 'danger')),
        ];
    }
}
