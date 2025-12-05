<?php

namespace App\Filament\Resources\SopCategories\Pages;

use App\Filament\Resources\SopCategories\SopCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSopCategory extends EditRecord
{
    protected static string $resource = SopCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
