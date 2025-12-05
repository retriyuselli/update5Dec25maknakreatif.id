<?php

namespace App\Filament\Resources\PembayaranPiutangs\Pages;

use App\Filament\Resources\PembayaranPiutangs\PembayaranPiutangResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranPiutang extends EditRecord
{
    protected static string $resource = PembayaranPiutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pembayaran Piutang berhasil diupdate')
            ->body('Perubahan data pembayaran piutang telah berhasil disimpan.');
    }
}
