<?php

namespace App\Filament\Resources\PembayaranPiutangs\Pages;

use App\Filament\Resources\PembayaranPiutangs\PembayaranPiutangResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPembayaranPiutang extends ViewRecord
{
    protected static string $resource = PembayaranPiutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
