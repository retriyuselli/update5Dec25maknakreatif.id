<?php

namespace App\Filament\Resources\DataPribadis\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\DataPribadis\DataPribadiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataPribadi extends EditRecord
{
    protected static string $resource = DataPribadiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
