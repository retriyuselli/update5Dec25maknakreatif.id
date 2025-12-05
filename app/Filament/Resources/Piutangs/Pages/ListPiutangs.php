<?php

namespace App\Filament\Resources\Piutangs\Pages;

use App\Filament\Resources\Piutangs\PiutangResource;
use App\Filament\Resources\Piutangs\Widgets\PiutangJatuhTempoWidget;
use App\Filament\Resources\Piutangs\Widgets\PiutangOverviewWidget;
use App\Filament\Resources\Piutangs\Widgets\TopDebiturWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiutangs extends ListRecords
{
    protected static string $resource = PiutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PiutangOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            PiutangJatuhTempoWidget::class,
            TopDebiturWidget::class,
        ];
    }
}
