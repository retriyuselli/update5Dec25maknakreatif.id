<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the user first
        $user = parent::handleRecordCreation($data);

        // Generate targets for Account Manager after user is created
        $this->generateTargetsForAccountManager($user);

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function generateTargetsForAccountManager($user)
    {
        // Check if user has Account Manager role after creation
        // Use a slight delay to ensure roles are properly assigned

        try {
            // Refresh the user to get the latest role assignments
            $user->refresh();
            $user->load('roles');

            // Check if user has Account Manager role
            if ($user->hasRole('Account Manager')) {
                // Generate targets for current year
                Artisan::call('targets:generate', [
                    '--auto-12-months' => true,
                    '--year' => date('Y'),
                ]);

                Notification::make()
                    ->title('Account Manager Created')
                    ->body('User created successfully and targets have been generated automatically.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('User Created')
                    ->body('User created successfully.')
                    ->success()
                    ->send();
            }

        } catch (Exception $e) {
            // Log error but don't fail the user creation
            Log::warning('Failed to auto-generate targets for new user: '.$e->getMessage());

            Notification::make()
                ->title('User Created')
                ->body('User created successfully. Targets can be generated manually if needed.')
                ->warning()
                ->send();
        }
    }
}
