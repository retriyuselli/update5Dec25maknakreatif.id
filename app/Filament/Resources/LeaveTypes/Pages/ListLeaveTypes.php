<?php

namespace App\Filament\Resources\LeaveTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\LeaveTypes\LeaveTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveTypes extends ListRecords
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
