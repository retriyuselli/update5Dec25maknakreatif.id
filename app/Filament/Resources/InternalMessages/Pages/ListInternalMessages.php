<?php

namespace App\Filament\Resources\InternalMessages\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\InternalMessages\InternalMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInternalMessages extends ListRecords
{
    protected static string $resource = InternalMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
