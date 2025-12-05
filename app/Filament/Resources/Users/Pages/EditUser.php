<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Check if current user can edit this record
        if (! UserResource::isSuperAdmin() && UserResource::isTargetUserSuperAdmin($this->record)) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki izin untuk mengedit user dengan role Super Admin.')
                ->danger()
                ->send();

            $this->redirect(UserResource::getUrl('index'));

            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(function () {
                    // Super admin can delete anyone
                    if (UserResource::isSuperAdmin()) {
                        return true;
                    }

                    // Non-super admin cannot delete super admin users
                    return ! UserResource::isTargetUserSuperAdmin($this->record);
                }),
        ];
    }
}
