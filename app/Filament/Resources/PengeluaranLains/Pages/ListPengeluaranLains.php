<?php

namespace App\Filament\Resources\PengeluaranLains\Pages;

use App\Filament\Resources\PengeluaranLains\PengeluaranLainResource;
use App\Filament\Resources\PengeluaranLains\Widgets\PengeluaranOverviewWidgets;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPengeluaranLains extends ListRecords
{
    protected static string $resource = PengeluaranLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengeluaran Lainnya')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PengeluaranOverviewWidgets::class,
        ];
    }
}
