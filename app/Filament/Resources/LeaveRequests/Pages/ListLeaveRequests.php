<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestResource;
use App\Filament\Resources\LeaveRequests\Widgets\LeaveRequestChart;
use App\Filament\Resources\LeaveRequests\Widgets\LeaveRequestOverview;
use App\Filament\Resources\LeaveRequests\Widgets\LeaveTypeStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LeaveRequestOverview::class,
            LeaveRequestChart::class,
            LeaveTypeStats::class,
        ];
    }
}
