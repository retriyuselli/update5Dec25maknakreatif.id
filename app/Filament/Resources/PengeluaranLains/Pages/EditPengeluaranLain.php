<?php

namespace App\Filament\Resources\PengeluaranLains\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PengeluaranLains\PengeluaranLainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengeluaranLain extends EditRecord
{
    protected static string $resource = PengeluaranLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
