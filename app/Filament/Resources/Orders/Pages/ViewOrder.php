<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(function (): bool {
                    $record = $this->getRecord();

                    // Jika status done, hanya super_admin yang bisa edit
                    if ($record->status === OrderStatus::Done) {
                        /** @var User $user */
                        $user = Auth::user();

                        return $user && $user->hasRole('super_admin');
                    }

                    // Status selain done, semua user bisa edit
                    return true;
                })
                ->tooltip(function (): string {
                    $record = $this->getRecord();

                    if ($record->status === OrderStatus::Done) {
                        /** @var User $user */
                        $user = Auth::user();
                        if (! ($user && $user->hasRole('super_admin'))) {
                            return 'Order sudah selesai. Hanya Super Admin yang dapat mengedit.';
                        }
                    }

                    return 'Edit order ini';
                }),
        ];
    }
}
