<?php

namespace App\Filament\Resources\Piutangs\Pages;

use App\Filament\Resources\Piutangs\PiutangResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePiutang extends CreateRecord
{
    protected static string $resource = PiutangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dibuat_oleh'] = Auth::id();

        // Set sisa_piutang sama dengan total_piutang saat buat baru
        $data['sisa_piutang'] = $data['total_piutang'];
        $data['sudah_dibayar'] = 0;

        return $data;
    }
}
