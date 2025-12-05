<?php

namespace App\Filament\Resources\ProspectApps\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ProspectApps\ProspectAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProspectApps extends ListRecords
{
    protected static string $resource = ProspectAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
