<?php

namespace App\Filament\Widgets;

use App\Models\LeaveRequest;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class LeaveUsageChartWidget extends ChartWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 51;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public function getHeading(): ?string
    {
        return 'Leave Usage Trends';
    }

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'year' => 'This Year',
            'quarter' => 'This Quarter',
            'month' => 'This Month',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;
        $currentYear = now()->year;

        switch ($filter) {
            case 'quarter':
                return $this->getQuarterlyData();
            case 'month':
                return $this->getMonthlyData();
            default:
                return $this->getYearlyData();
        }
    }

    protected function getYearlyData(): array
    {
        // Get monthly leave usage for the current year
        $monthlyData = LeaveRequest::selectRaw('
                MONTH(start_date) as month,
                COUNT(*) as total_requests,
                SUM(total_days) as total_days,
                leave_type_id
            ')
            ->whereYear('start_date', now()->year)
            ->where('status', 'approved')
            ->with('leaveType')
            ->groupBy('month', 'leave_type_id')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Initialize data arrays
        $annualLeaveData = array_fill(0, 12, 0);
        $sickLeaveData = array_fill(0, 12, 0);
        $otherLeaveData = array_fill(0, 12, 0);

        foreach ($monthlyData as $data) {
            $monthIndex = $data->month - 1;
            $leaveTypeName = $data->leaveType->name ?? 'Other';

            if (str_contains(strtolower($leaveTypeName), 'annual')) {
                $annualLeaveData[$monthIndex] = $data->total_days;
            } elseif (str_contains(strtolower($leaveTypeName), 'sick')) {
                $sickLeaveData[$monthIndex] = $data->total_days;
            } else {
                $otherLeaveData[$monthIndex] = $data->total_days;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Annual Leave',
                    'data' => $annualLeaveData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
                [
                    'label' => 'Sick Leave',
                    'data' => $sickLeaveData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'fill' => true,
                ],
                [
                    'label' => 'Other Leave',
                    'data' => $otherLeaveData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getQuarterlyData(): array
    {
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

        $quarterlyData = LeaveRequest::selectRaw('
                QUARTER(start_date) as quarter,
                SUM(total_days) as total_days,
                leave_type_id
            ')
            ->whereYear('start_date', now()->year)
            ->where('status', 'approved')
            ->with('leaveType')
            ->groupBy('quarter', 'leave_type_id')
            ->orderBy('quarter')
            ->get();

        $annualLeaveData = array_fill(0, 4, 0);
        $sickLeaveData = array_fill(0, 4, 0);

        foreach ($quarterlyData as $data) {
            $quarterIndex = $data->quarter - 1;
            $leaveTypeName = $data->leaveType->name ?? 'Other';

            if (str_contains(strtolower($leaveTypeName), 'annual')) {
                $annualLeaveData[$quarterIndex] = $data->total_days;
            } elseif (str_contains(strtolower($leaveTypeName), 'sick')) {
                $sickLeaveData[$quarterIndex] = $data->total_days;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Annual Leave',
                    'data' => $annualLeaveData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                ],
                [
                    'label' => 'Sick Leave',
                    'data' => $sickLeaveData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                ],
            ],
            'labels' => $quarters,
        ];
    }

    protected function getMonthlyData(): array
    {
        // Get daily data for current month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        $dailyData = LeaveRequest::selectRaw('
                DAY(start_date) as day,
                SUM(total_days) as total_days
            ')
            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'approved')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('total_days', 'day')
            ->toArray();

        $labels = [];
        $data = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $labels[] = $day;
            $data[] = $dailyData[$day] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Leave Requests',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
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
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Days',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => $this->filter === 'month' ? 'Day of Month' :
                                 ($this->filter === 'quarter' ? 'Quarter' : 'Month'),
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }

    public function getDescription(): ?string
    {
        return 'Visual representation of leave usage patterns across different time periods.';
    }
}
