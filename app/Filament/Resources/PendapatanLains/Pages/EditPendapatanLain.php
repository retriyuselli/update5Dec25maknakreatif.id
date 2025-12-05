<?php

namespace App\Filament\Resources\PendapatanLains\Pages;

use App\Filament\Resources\PendapatanLains\PendapatanLainResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPendapatanLain extends EditRecord
{
    protected static string $resource = PendapatanLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
