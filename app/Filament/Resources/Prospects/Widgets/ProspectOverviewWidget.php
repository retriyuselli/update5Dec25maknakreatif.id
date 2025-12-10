<?php

namespace App\Filament\Resources\Prospects\Widgets;

use App\Models\Prospect;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProspectOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $monthProspects = Prospect::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $withOrders = Prospect::whereHas('orders')->count();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekProspects = Prospect::whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $todayProspects = Prospect::whereDate('created_at', Carbon::today())->count();

        return [
            Stat::make('Dengan Order', $withOrders)
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success'),

            Stat::make('Prospek Bulan Ini', $monthProspects)
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Prospek Minggu Ini', $weekProspects)
                ->icon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Prospek Hari Ini', $todayProspects)
                ->icon('heroicon-o-clock')
                ->color('info'),
        ];
    }
}
