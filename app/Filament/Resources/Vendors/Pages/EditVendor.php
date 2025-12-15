<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug((string) $data['name']);
        }
        if (! empty($data['slug'])) {
            $exists = \App\Models\Vendor::where('slug', $data['slug'])
                ->where('id', '!=', $this->getRecord()->id)
                ->exists();

            if ($exists) {
                Notification::make()
                    ->danger()
                    ->title('Slug Duplikat')
                    ->body('Slug "'.($data['slug'] ?? '').'" sudah digunakan. Silakan ubah slug atau nama.')
                    ->persistent()
                    ->send();

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'slug' => 'Slug sudah digunakan',
                ]);
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Vendor')
                ->modalDescription('Are you sure you want to delete this vendor? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('danger')
                ->visible(function (): bool {
                    $record = $this->getRecord();
                    if (! $record) {
                        return false;
                    }

                    return $record->usage_status === 'Available';
                })
                ->before(function () {
                    $record = $this->getRecord();
                    if (! $record) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('Vendor data not found. Please refresh the page and try again.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    Notification::make()
                        ->info()
                        ->title('Processing')
                        ->body('Validating vendor for deletion...')
                        ->send();
                })
                ->action(function () {
                    $record = $this->getRecord();
                    if (! $record) {
                        Notification::make()
                            ->danger()
                            ->title('Deletion Failed')
                            ->body('Vendor data not found. May have been already deleted or moved.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    try {
                        $record->refresh();
                    } catch (Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Deletion Failed')
                            ->body('Cannot access vendor data. May have been deleted by another user.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Double check for associations
                    if ($record->usage_status === 'In Use') {
                        $usageDetails = $record->usage_details;
                        $details = [];
                        if ($usageDetails['productCount'] > 0) {
                            $details[] = "{$usageDetails['productCount']} product(s)";
                        }
                        if ($usageDetails['expenseCount'] > 0) {
                            $details[] = "{$usageDetails['expenseCount']} expense(s)";
                        }
                        if ($usageDetails['notaDinasCount'] > 0) {
                            $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                        }

                        Notification::make()
                            ->danger()
                            ->title('Deletion Not Allowed')
                            ->body("Vendor '{$record->name}' cannot be deleted because it is being used in ".implode(' and ', $details).'. Please remove these associations first.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    try {
                        $vendorName = $record->name ?? 'Unknown Vendor';
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Vendor Successfully Deleted')
                            ->body("'{$vendorName}' has been deleted from the system.")
                            ->duration(5000)
                            ->send();

                        // Redirect to index after successful deletion
                        return redirect($this->getResource()::getUrl('index'));

                    } catch (QueryException $e) {
                        $errorCode = $e->getCode();
                        if ($errorCode === '23000') {
                            Notification::make()
                                ->danger()
                                ->title('Deletion Failed - Data Constraint')
                                ->body('This vendor cannot be deleted because it is referenced by other data in the system.')
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Database Error')
                                ->body('A database error occurred while deleting the vendor. Please try again later.')
                                ->persistent()
                                ->send();
                        }

                        return false;

                    } catch (ModelNotFoundException $e) {
                        Notification::make()
                            ->warning()
                            ->title('Vendor Already Deleted')
                            ->body('This vendor appears to have been already deleted by another user.')
                            ->send();

                        return false;

                    } catch (Exception $e) {
                        Log::error('Vendor deletion failed', [
                            'vendor_id' => $record->id ?? 'unknown',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Unexpected Error')
                            ->body('An unexpected error occurred while deleting the vendor. System administrator has been notified.')
                            ->persistent()
                            ->send();

                        return false;
                    }
                }),

            Action::make('cannot_delete')
                ->label('Cannot Delete')
                ->icon('heroicon-m-shield-exclamation')
                ->color('gray')
                ->tooltip('This vendor cannot be deleted because it is being used')
                ->visible(function (): bool {
                    $record = $this->getRecord();
                    if (! $record) {
                        return false;
                    }

                    return $record->usage_status === 'In Use';
                })
                ->action(function () {
                    $record = $this->getRecord();
                    $usageDetails = $record->usage_details;

                    $details = [];
                    if ($usageDetails['productCount'] > 0) {
                        $details[] = "{$usageDetails['productCount']} product(s)";
                    }
                    if ($usageDetails['expenseCount'] > 0) {
                        $details[] = "{$usageDetails['expenseCount']} expense(s)";
                    }
                    if ($usageDetails['notaDinasCount'] > 0) {
                        $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                    }

                    Notification::make()
                        ->warning()
                        ->title('Cannot Delete Vendor')
                        ->body("'{$record->name}' cannot be deleted because it has associated ".implode(' and ', $details).'. Please remove these associations first.')
                        ->persistent()
                        ->send();
                }),

            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
