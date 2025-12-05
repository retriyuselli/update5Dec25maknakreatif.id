<?php

namespace App\Filament\Resources\LeaveBalances\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\LeaveBalances\LeaveBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveBalance extends EditRecord
{
    protected static string $resource = LeaveBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
