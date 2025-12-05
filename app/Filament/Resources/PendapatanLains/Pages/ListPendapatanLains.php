<?php

namespace App\Filament\Resources\PendapatanLains\Pages;

use App\Filament\Resources\PendapatanLains\PendapatanLainResource;
use App\Filament\Resources\PendapatanLains\Widgets\PendapatanLainOverviewWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPendapatanLains extends ListRecords
{
    protected static string $resource = PendapatanLainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PendapatanLainOverviewWidget::class,
        ];
    }
}
