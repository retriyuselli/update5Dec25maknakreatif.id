<?php

namespace App\Filament\Resources\PendapatanLains\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PendapatanLains\PendapatanLainResource;
use Filament\Actions;
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
