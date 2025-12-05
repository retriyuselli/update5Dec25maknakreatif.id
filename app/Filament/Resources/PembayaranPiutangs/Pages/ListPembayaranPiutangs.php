<?php

namespace App\Filament\Resources\PembayaranPiutangs\Pages;

use App\Filament\Resources\PembayaranPiutangs\PembayaranPiutangResource;
use Filament\Actions\CreateAction;
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
