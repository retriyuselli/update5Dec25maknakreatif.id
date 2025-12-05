<?php

namespace App\Filament\Resources\FixedAssets\Pages;

use App\Filament\Resources\FixedAssets\FixedAssetResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFixedAsset extends EditRecord
{
    protected static string $resource = FixedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calculate_depreciation')
                ->label('Calculate Depreciation')
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->action(function () {
                    if (! $this->record->isFullyDepreciated()) {
                        $monthlyDepreciation = $this->record->calculateMonthlyDepreciation();
                        $this->record->accumulated_depreciation += $monthlyDepreciation;
                        $this->record->updateBookValue();

                        Notification::make()
                            ->title('Depreciation Calculated')
                            ->body('Monthly depreciation: IDR '.number_format($monthlyDepreciation))
                            ->success()
                            ->send();

                        $this->refreshFormData(['accumulated_depreciation', 'current_book_value']);
                    } else {
                        Notification::make()
                            ->title('Asset Fully Depreciated')
                            ->body('This asset is already fully depreciated.')
                            ->warning()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record && ! $this->record->isFullyDepreciated()),

            Action::make('depreciation_history')
                ->label('Depreciation History')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->url(fn () => static::getResource()::getUrl('depreciation-history', ['record' => $this->record])),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate book value if purchase price changed
        if (isset($data['purchase_price']) && $data['purchase_price'] != $this->record->purchase_price) {
            $depreciationRate = $this->record->accumulated_depreciation / max($this->record->purchase_price, 1);
            $data['accumulated_depreciation'] = $data['purchase_price'] * $depreciationRate;
            $data['current_book_value'] = $data['purchase_price'] - $data['accumulated_depreciation'];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Fixed Asset Updated')
            ->body("Asset {$this->record->asset_code} has been successfully updated.")
            ->success()
            ->send();
    }
}
