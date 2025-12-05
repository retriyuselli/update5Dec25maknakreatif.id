<?php

namespace App\Filament\Resources\AccountManagerTargets\Widgets;

use App\Models\AccountManagerTarget;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AmPerformanceChart extends ChartWidget
{
    protected ?string $heading = 'Performance Trend 2024';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // Get monthly data for current year
        $monthlyData = AccountManagerTarget::query()
            ->where('year', $currentYear)
            ->selectRaw('month, SUM(target_amount) as total_target, SUM(achieved_amount) as total_achieved')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $targets = [];
        $achievements = [];

        // Initialize all months
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            $months[] = $monthName;
            $targets[] = 0;
            $achievements[] = 0;
        }

        // Fill actual data
        foreach ($monthlyData as $data) {
            $monthIndex = $data->month - 1;
            $targets[$monthIndex] = $data->total_target / 1000000; // Convert to millions
            $achievements[$monthIndex] = $data->total_achieved / 1000000;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Target (Juta Rp)',
                    'data' => $targets,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Achievement (Juta Rp)',
                    'data' => $achievements,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Amount (Millions)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
