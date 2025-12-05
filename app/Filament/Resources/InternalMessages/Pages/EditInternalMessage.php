<?php

namespace App\Filament\Resources\InternalMessages\Pages;

use App\Filament\Resources\InternalMessages\InternalMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInternalMessage extends EditRecord
{
    protected static string $resource = InternalMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
