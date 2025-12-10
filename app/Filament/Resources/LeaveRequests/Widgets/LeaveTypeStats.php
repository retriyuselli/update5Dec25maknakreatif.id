<?php

namespace App\Filament\Resources\LeaveRequests\Widgets;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class LeaveTypeStats extends ChartWidget
{
    protected ?string $heading = 'Distribusi Jenis Cuti';

    protected ?string $description = 'Statistik permohonan cuti berdasarkan jenis (tahun ini)';

    protected static ?int $sort = 3;

    // protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        // Base query - filter berdasarkan role
        $baseQuery = LeaveRequest::query()
            ->whereYear('start_date', $currentYear)
            ->where('status', 'approved'); // Hanya yang approved

        if ($user && ! $user->roles->contains('name', 'super_admin')) {
            $baseQuery->where('user_id', $user->id);
        }

        // Ambil data per jenis cuti
        $leaveTypeStats = $baseQuery
            ->join('leave_types', 'leave_requests.leave_type_id', '=', 'leave_types.id')
            ->selectRaw('leave_types.name, COUNT(*) as total_requests, SUM(leave_requests.total_days) as total_days')
            ->groupBy('leave_types.id', 'leave_types.name')
            ->orderBy('total_requests', 'desc')
            ->get();

        $labels = [];
        $requestsData = [];
        $daysData = [];
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // blue
            'rgba(34, 197, 94, 0.8)',    // green
            'rgba(251, 191, 36, 0.8)',   // yellow
            'rgba(239, 68, 68, 0.8)',    // red
            'rgba(147, 51, 234, 0.8)',   // purple
            'rgba(236, 72, 153, 0.8)',   // pink
            'rgba(14, 165, 233, 0.8)',   // sky
            'rgba(99, 102, 241, 0.8)',   // indigo
        ];

        foreach ($leaveTypeStats as $index => $stat) {
            $labels[] = $stat->name;
            $requestsData[] = $stat->total_requests;
            $daysData[] = $stat->total_days;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permohonan',
                    'data' => $requestsData,
                    'backgroundColor' => array_slice($colors, 0, count($requestsData)),
                    'borderColor' => array_map(function ($color) {
                        return str_replace('0.8', '1', $color);
                    }, array_slice($colors, 0, count($requestsData))),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Chart type: bar, line, pie, doughnut, etc.
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Distribusi Jenis Cuti ('.Carbon::now()->year.')',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed.y || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return label + ": " + value + " permohonan (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    // Refresh setiap 60 detik
    // protected ?string $pollingInterval = '60s';
}
