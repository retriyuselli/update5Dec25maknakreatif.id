<?php

namespace App\Filament\Resources\SopCategories\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SopCategories\SopCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSopCategories extends ListRecords
{
    protected static string $resource = SopCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
