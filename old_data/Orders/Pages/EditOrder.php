<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->visible(Auth::user()->hasRole('super_admin'))
                ->color('danger'),
            Action::make('Invoice')
                ->label('Detail')
                ->color('success')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => OrderResource::getUrl('invoice', ['record' => $record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
