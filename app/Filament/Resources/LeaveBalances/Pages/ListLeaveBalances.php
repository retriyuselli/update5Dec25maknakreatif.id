<?php

namespace App\Filament\Resources\LeaveBalances\Pages;

use App\Filament\Resources\LeaveBalances\LeaveBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveBalances extends ListRecords
{
    protected static string $resource = LeaveBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
