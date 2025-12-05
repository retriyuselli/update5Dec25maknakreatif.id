<?php

namespace App\Filament\Resources\Sops\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Sops\SopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSops extends ListRecords
{
    protected static string $resource = SopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
