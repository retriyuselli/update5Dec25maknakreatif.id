<?php

namespace App\Filament\Resources\Employees\Widgets;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class EmployeeOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get active employees count
        $activeEmployees = Employee::query()
            ->where('date_of_join', '<=', now())
            ->where(function ($query) {
                $query->whereNull('date_of_out')
                    ->orWhere('date_of_out', '>=', now());
            })
            ->count();

        // Get employees by role
        $employeesByRole = Employee::query()
            ->where('date_of_join', '<=', now())
            ->where(function ($query) {
                $query->whereNull('date_of_out')
                    ->orWhere('date_of_out', '>=', now());
            })
            ->select('position', DB::raw('COUNT(*) as count'))
            ->groupBy('position')
            ->get()
            ->pluck('count', 'position')
            ->toArray();

        // Get upcoming birthdays (next 30 days)
        $upcomingBirthdays = Employee::query()
            ->where('date_of_join', '<=', now())
            ->where(function ($query) {
                $query->whereNull('date_of_out')
                    ->orWhere('date_of_out', '>=', now());
            })
            ->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") BETWEEN DATE_FORMAT(NOW(), "%m-%d") AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 30 DAY), "%m-%d")')
            ->orderByRaw('DATE_FORMAT(date_of_birth, "%m-%d")')
            ->get();

        // Calculate top 5 event managers by project count
        $topEventManagers = Employee::query()
            ->where('position', 'Event Manager')
            ->where('date_of_join', '<=', now())
            ->where(function ($query) {
                $query->whereNull('date_of_out')
                    ->orWhere('date_of_out', '>=', now());
            })
            ->withCount(['orders' => function ($query) {
                $query->where('closing_date', '>=', now()->subYear());
            }])
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        // Get anniversary milestones this month (work anniversaries)
        $workAnniversaries = Employee::query()
            ->where('date_of_join', '<=', now())
            ->where(function ($query) {
                $query->whereNull('date_of_out')
                    ->orWhere('date_of_out', '>=', now());
            })
            ->whereRaw('MONTH(date_of_join) = MONTH(NOW())')
            ->whereRaw('DAY(date_of_join) >= DAY(NOW())')
            ->whereRaw('DATEDIFF(NOW(), date_of_join) >= 365')
            ->orderByRaw('DAY(date_of_join)')
            ->get()
            ->map(function ($employee) {
                $yearsOfService = Carbon::parse($employee->date_of_join)->diffInYears(now());

                return [
                    'name' => $employee->name,
                    'years' => $yearsOfService,
                    'anniversary_date' => Carbon::parse($employee->date_of_join)->setYear(date('Y'))->format('d M'),
                ];
            });

        // Construct stats array
        return [
            Stat::make('Active Employees', $activeEmployees)
                ->description('Current workforce strength')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart(array_values($employeesByRole))
                ->color('success'),

            Stat::make('Top Event Managers', 'Performance leaders')
                ->description($topEventManagers->isNotEmpty()
                    ? $topEventManagers->first()->name.' ('.$topEventManagers->first()->orders_count.' projects)'
                    : 'No data available')
                ->chart($topEventManagers->pluck('orders_count')->toArray())
                ->color('primary'),

            Stat::make('Upcoming Birthdays', $upcomingBirthdays->count())
                ->description($upcomingBirthdays->isNotEmpty()
                    ? 'Next: '.$upcomingBirthdays->first()->name.' ('.Carbon::parse($upcomingBirthdays->first()->date_of_birth)->format('d M').')'
                    : 'No upcoming birthdays')
                ->descriptionIcon('heroicon-m-cake')
                ->color('warning'),

            Stat::make('Work Anniversaries', $workAnniversaries->count())
                ->description($workAnniversaries->isNotEmpty()
                    ? $workAnniversaries->first()['name'].' - '.$workAnniversaries->first()['years'].' years on '.$workAnniversaries->first()['anniversary_date']
                    : 'No work anniversaries this month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
