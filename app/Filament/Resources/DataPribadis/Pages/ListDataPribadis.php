<?php

namespace App\Filament\Resources\DataPribadis\Pages;

use App\Filament\Resources\DataPribadis\DataPribadiResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataPribadis extends ListRecords
{
    protected static string $resource = DataPribadiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('linkToDataPribadi')
                ->label('Link to Data Pribadi')
                ->icon('heroicon-o-link')
                ->url(route('data-pribadi.create')) // Menggunakan nama rute yang benar
                ->color('primary'),
        ];
    }
}
