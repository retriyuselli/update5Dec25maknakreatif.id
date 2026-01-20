<?php

namespace App\Filament\Resources\LeaveTypes\Pages;

use App\Filament\Resources\LeaveTypes\LeaveTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Illuminate\Support\Facades\Auth;

class ListLeaveTypes extends ListRecords
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => Auth::user()->roles->contains('name', 'super_admin')),
        ];
    }
}
