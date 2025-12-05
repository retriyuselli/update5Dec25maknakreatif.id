<?php

namespace App\Filament\Resources\Prospects\Pages;

use App\Filament\Resources\Prospects\ProspectResource;
use App\Models\Prospect;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EditProspect extends EditRecord
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Prospect')
                ->modalDescription('Are you sure you want to delete this prospect? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('danger')
                ->visible(function (): bool {
                    /** @var Prospect $record */
                    $record = $this->getRecord();

                    // Hide delete button if prospect has orders
                    if (! $record || $record->orders()->exists()) {
                        return false;
                    }

                    // Show delete button only for warm prospects (no orders)
                    return true;
                })
                ->before(function () {
                    /** @var Prospect $record */
                    $record = $this->getRecord();

                    // Validate record exists before showing confirmation
                    if (! $record) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('Prospect record not found. Please refresh the page and try again.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Show loading notification
                    Notification::make()
                        ->info()
                        ->title('Processing')
                        ->body('Validating prospect for deletion...')
                        ->send();
                })
                ->action(function () {
                    /** @var Prospect $record */
                    $record = $this->getRecord();

                    // Comprehensive null and existence checks
                    if (! $record) {
                        Notification::make()
                            ->danger()
                            ->title('Deletion Failed')
                            ->body('Prospect record not found. It may have been already deleted or moved.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Refresh record from database to ensure latest state
                    try {
                        $record->refresh();
                    } catch (Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Deletion Failed')
                            ->body('Unable to access prospect record. It may have been deleted by another user.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Double check for associated orders
                    if ($record->orders()->exists()) {
                        $orderCount = $record->orders()->count();
                        Notification::make()
                            ->danger()
                            ->title('Deletion Not Allowed')
                            ->body("Prospect '{$record->name_event}' cannot be deleted because it has {$orderCount} associated order(s). Please remove the orders first.")
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Attempt deletion with comprehensive error handling
                    try {
                        $eventName = $record->name_event ?? 'Unknown Event';
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Prospect Deleted Successfully')
                            ->body("'{$eventName}' has been deleted from the system.")
                            ->duration(5000)
                            ->send();

                        // Redirect to index after successful deletion
                        $this->redirect(ProspectResource::getUrl('index'));

                    } catch (QueryException $e) {
                        // Handle database-specific errors
                        $errorCode = $e->getCode();
                        if ($errorCode === '23000') {
                            Notification::make()
                                ->danger()
                                ->title('Deletion Failed - Data Constraint')
                                ->body('This prospect cannot be deleted because it is referenced by other records in the system.')
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Database Error')
                                ->body('A database error occurred while deleting the prospect. Please try again later.')
                                ->persistent()
                                ->send();
                        }

                        return false;

                    } catch (ModelNotFoundException $e) {
                        Notification::make()
                            ->warning()
                            ->title('Prospect Already Deleted')
                            ->body('This prospect appears to have been already deleted by another user.')
                            ->send();

                        return false;

                    } catch (Exception $e) {
                        // Log the error for debugging
                        Log::error('Prospect deletion failed from edit page', [
                            'prospect_id' => $record->id ?? 'unknown',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Unexpected Error')
                            ->body('An unexpected error occurred while deleting the prospect. The system administrator has been notified.')
                            ->persistent()
                            ->send();

                        return false;
                    }
                }),

            // Add informational action for prospects with orders
            Action::make('cannot_delete_info')
                ->label('Cannot Delete')
                ->icon('heroicon-m-shield-exclamation')
                ->color('gray')
                ->tooltip('This prospect cannot be deleted because it has associated orders')
                ->visible(function (): bool {
                    /** @var Prospect $record */
                    $record = $this->getRecord();

                    // Show this action only for prospects with orders
                    return $record && $record->orders()->exists();
                })
                ->action(function () {
                    /** @var Prospect $record */
                    $record = $this->getRecord();

                    if ($record && $record->orders()->exists()) {
                        $orderCount = $record->orders()->count();
                        Notification::make()
                            ->warning()
                            ->title('Cannot Delete Prospect')
                            ->body("'{$record->name_event}' cannot be deleted because it has {$orderCount} associated order(s). Please remove the orders first.")
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
