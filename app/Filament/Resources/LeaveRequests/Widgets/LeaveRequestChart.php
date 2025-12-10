<?php

namespace App\Filament\Resources\LeaveRequests\Widgets;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class LeaveRequestChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Permohonan Cuti';

    protected ?string $description = 'Data permohonan cuti 12 bulan terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = Auth::user();

        // Base query - filter berdasarkan role
        $baseQuery = LeaveRequest::query();
        if ($user && ! $user->roles->contains('name', 'super_admin')) {
            $baseQuery->where('user_id', $user->id);
        }

        // Data 7 bulan terakhir
        $data = [];
        $labels = [];
        $pendingData = [];
        $approvedData = [];
        $rejectedData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $labels[] = $monthYear;

            $monthQuery = (clone $baseQuery)
                ->whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month);

            $pendingData[] = (clone $monthQuery)->where('status', 'pending')->count();
            $approvedData[] = (clone $monthQuery)->where('status', 'approved')->count();
            $rejectedData[] = (clone $monthQuery)->where('status', 'rejected')->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Menunggu',
                    'data' => $pendingData,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.8)', // warning color
                    'borderColor' => 'rgba(251, 191, 36, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Disetujui',
                    'data' => $approvedData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // success color
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Ditolak',
                    'data' => $rejectedData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // danger color
                    'borderColor' => 'rgba(239, 68, 68, 1)',
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
                    'text' => 'Trend Permohonan Cuti 12 Bulan Terakhir',
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
