<?php

namespace App\Filament\Resources\Piutangs\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Piutangs\Widgets\PiutangOverviewWidget;
use App\Filament\Resources\Piutangs\Widgets\PiutangJatuhTempoWidget;
use App\Filament\Resources\Piutangs\Widgets\TopDebiturWidget;
use App\Filament\Resources\Piutangs\PiutangResource;
use Filament\Actions;
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
