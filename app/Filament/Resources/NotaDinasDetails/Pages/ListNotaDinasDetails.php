<?php

namespace App\Filament\Resources\NotaDinasDetails\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\NotaDinasDetails\NotaDinasDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotaDinasDetails extends ListRecords
{
    protected static string $resource = NotaDinasDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
