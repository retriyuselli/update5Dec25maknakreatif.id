<?php

namespace App\Filament\Resources\Industries\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Industries\IndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIndustries extends ListRecords
{
    protected static string $resource = IndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
