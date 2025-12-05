<?php

namespace App\Filament\Resources\CompanyLogos\Pages;

use App\Filament\Resources\CompanyLogos\CompanyLogoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyLogos extends ListRecords
{
    protected static string $resource = CompanyLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
