<?php

namespace App\Filament\Resources\SimulasiProduks\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SimulasiProduks\SimulasiProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimulasiProduks extends ListRecords
{
    protected static string $resource = SimulasiProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
