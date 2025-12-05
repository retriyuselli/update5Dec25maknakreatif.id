<?php

namespace App\Filament\Resources\ProspectApps\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ProspectApps\ProspectAppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProspectApp extends EditRecord
{
    protected static string $resource = ProspectAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
