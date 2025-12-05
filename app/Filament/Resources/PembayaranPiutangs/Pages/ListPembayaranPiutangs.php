<?php

namespace App\Filament\Resources\PembayaranPiutangs\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PembayaranPiutangs\PembayaranPiutangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranPiutangs extends ListRecords
{
    protected static string $resource = PembayaranPiutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
