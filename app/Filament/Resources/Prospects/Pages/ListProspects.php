<?php

namespace App\Filament\Resources\Prospects\Pages;

use Filament\Actions\Action;
use App\Models\Prospect;
use Exception;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Prospects\ProspectResource;
use App\Filament\Widgets\ProspectStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProspects extends ListRecords
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_prospects_without_orders')
                ->label('Prospect Tanpa Order')
                ->icon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->badge(function () {
                    try {
                        return Prospect::doesntHave('orders')->count();
                    } catch (Exception $e) {
                        return 0;
                    }
                })
                ->url(fn () => static::getUrl(['tableFilters' => ['order_status' => ['value' => 'no_order']]])),

            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProspectStatsWidget::class,
        ];
    }
}
