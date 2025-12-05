<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\AccountManagerStats;
use App\Filament\Resources\Users\Widgets\UserExpirationOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserExpirationOverview::class,
            // AccountManagerStats::class,
        ];
    }
}
