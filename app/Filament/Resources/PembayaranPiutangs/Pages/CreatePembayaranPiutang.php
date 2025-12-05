<?php

namespace App\Filament\Resources\PembayaranPiutangs\Pages;

use App\Filament\Resources\PembayaranPiutangs\PembayaranPiutangResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaranPiutang extends CreateRecord
{
    protected static string $resource = PembayaranPiutangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pembayaran Piutang berhasil dicatat')
            ->body('Pembayaran piutang telah berhasil disimpan.');
    }
}
