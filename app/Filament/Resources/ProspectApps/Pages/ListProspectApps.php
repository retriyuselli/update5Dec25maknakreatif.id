<?php

namespace App\Filament\Resources\ProspectApps\Pages;

use App\Filament\Resources\ProspectApps\ProspectAppResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListProspectApps extends ListRecords
{
    protected static string $resource = ProspectAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('prospectApp')
                ->label('Prospect App')
                ->icon('heroicon-o-link')
                ->color('primary')
                ->url('https://update.maknakreatif.id/prospect-app')
                ->openUrlInNewTab(),
        ];
    }
}
