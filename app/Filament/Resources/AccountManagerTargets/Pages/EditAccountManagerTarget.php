<?php

namespace App\Filament\Resources\AccountManagerTargets\Pages;

use App\Filament\Resources\AccountManagerTargets\AccountManagerTargetResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAccountManagerTarget extends EditRecord
{
    protected static string $resource = AccountManagerTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh_achieved')
                ->label('Refresh Pencapaian')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->action(function () {
                    $record = $this->getRecord();
                    $achieved = Order::where('user_id', $record->user_id)
                        ->whereYear('closing_date', $record->year)
                        ->whereMonth('closing_date', $record->month)
                        ->sum('grand_total') ?? 0;

                    $record->update(['achieved_amount' => $achieved]);

                    $this->fillForm();

                    Notification::make()
                        ->title('Pencapaian berhasil di-refresh')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Auto-calculate achieved amount when loading the form
        $record = $this->getRecord();
        $achieved = Order::where('user_id', $record->user_id)
            ->whereYear('closing_date', $record->year)
            ->whereMonth('closing_date', $record->month)
            ->sum('grand_total') ?? 0;

        $data['achieved_amount'] = $achieved;

        return $data;
    }
}
