<?php

namespace App\Filament\Resources\ChartOfAccounts\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ChartOfAccounts\ChartOfAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChartOfAccounts extends ListRecords
{
    protected static string $resource = ChartOfAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
