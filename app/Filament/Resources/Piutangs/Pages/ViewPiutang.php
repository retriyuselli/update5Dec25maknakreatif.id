<?php

namespace App\Filament\Resources\Piutangs\Pages;

use App\Filament\Resources\Piutangs\PiutangResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPiutang extends ViewRecord
{
    protected static string $resource = PiutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('terima_pembayaran')
                ->label('Terima Pembayaran')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                // ->url(fn () => \App\Filament\Resources\PembayaranPiutangResource::getUrl('create', ['piutang_id' => $this->record->id]))
                ->visible(fn () => in_array($this->record->status, ['aktif', 'dibayar_sebagian', 'jatuh_tempo'])),
        ];
    }
}
