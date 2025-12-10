<?php

namespace App\Filament\Resources\LeaveRequests\Widgets;

use App\Models\LeaveRequest;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeaveRequestOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        // Base query - filter berdasarkan role
        $baseQuery = LeaveRequest::query();
        if ($user && ! $user->roles->contains('name', 'super_admin')) {
            $baseQuery->where('user_id', $user->id);
        }

        // Stats untuk tahun ini
        $thisYearQuery = (clone $baseQuery)->whereYear('start_date', $currentYear);

        // Total leave requests
        $totalRequests = (clone $baseQuery)->count();
        $thisYearRequests = (clone $thisYearQuery)->count();

        // Pending requests
        $pendingRequests = (clone $baseQuery)->where('status', 'pending')->count();
        $pendingThisYear = (clone $thisYearQuery)->where('status', 'pending')->count();

        // Approved requests
        $approvedRequests = (clone $baseQuery)->where('status', 'approved')->count();
        $approvedThisYear = (clone $thisYearQuery)->where('status', 'approved')->count();

        // Rejected requests
        $rejectedRequests = (clone $baseQuery)->where('status', 'rejected')->count();
        $rejectedThisYear = (clone $thisYearQuery)->where('status', 'rejected')->count();

        // Total days taken this year (approved only)
        $totalDaysTaken = (clone $thisYearQuery)
            ->where('status', 'approved')
            ->sum('total_days');

        // Most used leave type this year
        $mostUsedLeaveType = (clone $thisYearQuery)
            ->where('status', 'approved')
            ->join('leave_types', 'leave_requests.leave_type_id', '=', 'leave_types.id')
            ->selectRaw('leave_types.name, COUNT(*) as count')
            ->groupBy('leave_types.id', 'leave_types.name')
            ->orderByDesc('count')
            ->first();

        return [
            Stat::make('Total Permohonan Cuti', $totalRequests)
                ->description($thisYearRequests.' permohonan tahun '.$currentYear)
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Menunggu Persetujuan', $pendingRequests)
                ->description($pendingThisYear.' pending tahun ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Disetujui', $approvedRequests)
                ->description($approvedThisYear.' disetujui tahun ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ditolak', $rejectedRequests)
                ->description($rejectedThisYear.' ditolak tahun ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Hari Cuti Diambil', $totalDaysTaken.' hari')
                ->description('Tahun '.$currentYear.' (disetujui)')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Jenis Cuti Terpopuler', $mostUsedLeaveType ? $mostUsedLeaveType->name : 'Belum ada data')
                ->description($mostUsedLeaveType ? $mostUsedLeaveType->count.' kali digunakan' : 'Tahun '.$currentYear)
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),
        ];
    }

    protected function getColumns(): int
    {
        return 3; // 3 kolom untuk layout yang rapi
    }

    // Refresh setiap 30 detik untuk data real-time
    // protected ?string $pollingInterval = '30s';
}
