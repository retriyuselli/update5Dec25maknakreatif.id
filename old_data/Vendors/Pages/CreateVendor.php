<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
// use Filament\Facades\Filament; // removed, using Notification::make instead
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['priceHistories'])) {
            $activeCount = collect($data['priceHistories'])
                ->filter(fn ($item) => ($item['status'] ?? null) === 'active')
                ->count();

            if ($activeCount > 1) {
                \Filament\Notifications\Notification::make()
                    ->danger()
                    ->title('Tidak dapat membuat vendor')
                    ->body('Hanya satu riwayat harga dapat berstatus active untuk setiap vendor.')
                    ->persistent()
                    ->send();
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'priceHistories' => 'Hanya satu riwayat harga dapat berstatus active untuk setiap vendor.',
                ]);
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
