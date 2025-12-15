<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use App\Models\Vendor;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function handleRecordCreation(array $data): Vendor
    {
        try {
            if (empty($data['slug']) && ! empty($data['name'])) {
                $data['slug'] = Str::slug((string) $data['name']);
            }
            if (! empty($data['slug'])) {
                $exists = Vendor::where('slug', $data['slug'])->exists();
                if ($exists) {
                    Notification::make()
                        ->danger()
                        ->title('Slug Duplikat')
                        ->body('Slug "'.($data['slug'] ?? '').'" sudah digunakan. Silakan ubah nama.')
                        ->persistent()
                        ->send();
                    throw ValidationException::withMessages([
                        'slug' => 'Slug sudah digunakan',
                    ]);
                }
            }
            return Vendor::create($data);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                Notification::make()
                    ->danger()
                    ->title('Slug Duplikat')
                    ->body('Slug "'.($data['slug'] ?? '').'" sudah digunakan. Silakan ubah slug atau nama.')
                    ->persistent()
                    ->send();
            }
            throw $e;
        }
    }
}
