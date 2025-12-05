<?php

namespace App\Filament\Resources\PaymentMethods\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PaymentMethods\Widgets\PaymentMethodStatsWidget;
use App\Filament\Resources\PaymentMethods\PaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethods extends ListRecords
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentMethodStatsWidget::class,
        ];
    }
}
