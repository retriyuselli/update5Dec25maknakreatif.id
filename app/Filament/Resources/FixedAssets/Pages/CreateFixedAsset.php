<?php

namespace App\Filament\Resources\FixedAssets\Pages;

use App\Filament\Resources\FixedAssets\FixedAssetResource;
use App\Models\FixedAsset;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFixedAsset extends CreateRecord
{
    protected static string $resource = FixedAssetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate asset code if not provided
        if (empty($data['asset_code'])) {
            $data['asset_code'] = FixedAsset::generateAssetCode($data['category'] ?? '');
        }

        // Set initial book value to purchase price
        $data['current_book_value'] = $data['purchase_price'];

        // Ensure accumulated depreciation starts at 0
        $data['accumulated_depreciation'] = 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Send notification
        Notification::make()
            ->title('Fixed Asset Created')
            ->body("Asset {$this->record->asset_code} has been successfully created.")
            ->success()
            ->send();
    }
}
