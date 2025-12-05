<?php

namespace App\Filament\Resources\ExpenseOps\Pages;

use App\Filament\Resources\ExpenseOps\ExpenseOpsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpenseOps extends EditRecord
{
    protected static string $resource = ExpenseOpsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
