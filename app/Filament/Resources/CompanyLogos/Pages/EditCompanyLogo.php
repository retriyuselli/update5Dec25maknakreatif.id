<?php

namespace App\Filament\Resources\CompanyLogos\Pages;

use App\Filament\Resources\CompanyLogos\CompanyLogoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyLogo extends EditRecord
{
    protected static string $resource = CompanyLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
