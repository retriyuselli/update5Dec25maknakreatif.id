<?php

namespace App\Filament\Resources\NotaDinas\Pages;

use App\Filament\Resources\NotaDinas\NotaDinasResource;
use App\Models\NotaDinas;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditNotaDinas extends EditRecord
{
    protected static string $resource = NotaDinasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Nota Dinas')
                ->modalDescription('Are you sure you want to delete this Nota Dinas? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('danger')
                ->visible(function (): bool {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();
                    $detailCount = $record->details()->count();

                    return $detailCount === 0;
                })
                ->before(function () {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();
                    $detailCount = $record->details()->count();

                    if ($detailCount > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Nota Dinas')
                            ->body("This Nota Dinas cannot be deleted because it has {$detailCount} related detail record(s). Please remove all details first.")
                            ->persistent()
                            ->send();

                        return false;
                    }
                }),
            Action::make('view_details')
                ->label('View Details')
                ->icon('heroicon-o-list-bullet')
                ->color('info')
                ->visible(function (): bool {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();
                    $detailCount = $record->details()->count();

                    return $detailCount > 0;
                })
                ->modalHeading(function (): string {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();

                    return 'Nota Dinas Details - '.$record->no_nd;
                })
                ->modalDescription('This Nota Dinas has related details and cannot be deleted.')
                ->modalContent(function (): HtmlString {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();
                    $details = $record->details()->with('vendor', 'order')->get();

                    $content = '<div class="space-y-4">';
                    $content .= '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">';
                    $content .= '<h3 class="font-semibold text-yellow-800 mb-3">⚠️ Protected from Deletion</h3>';
                    $content .= '<p class="text-sm text-yellow-700 mb-3">This Nota Dinas has '.$details->count().' related detail record(s) and cannot be deleted.</p>';

                    if ($details->count() > 0) {
                        $content .= '<div class="space-y-2">';
                        foreach ($details as $detail) {
                            $content .= '<div class="border-l-4 border-yellow-400 pl-3 py-2 bg-white rounded">';
                            $content .= '<p class="text-sm font-medium">Purpose: '.($detail->keperluan ?? 'Not specified').'</p>';
                            $content .= '<p class="text-sm text-gray-600">Amount: Rp '.number_format($detail->jumlah_transfer, 0, ',', '.').'</p>';
                            if ($detail->vendor) {
                                $content .= '<p class="text-sm text-gray-600">Vendor: '.$detail->vendor->name.'</p>';
                            }
                            $content .= '</div>';
                        }
                        $content .= '</div>';
                    }

                    $content .= '</div>';
                    $content .= '</div>';

                    return new HtmlString($content);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
            RestoreAction::make()
                ->requiresConfirmation()
                ->modalHeading('Restore Nota Dinas')
                ->modalDescription('Are you sure you want to restore this deleted Nota Dinas?')
                ->modalSubmitActionLabel('Yes, restore')
                ->modalIcon('heroicon-o-arrow-path')
                ->modalIconColor('success')
                ->successNotificationTitle('Nota Dinas Restored'),
            ForceDeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Permanently Delete Nota Dinas')
                ->modalDescription('Are you sure you want to PERMANENTLY delete this Nota Dinas? This action cannot be undone and will also delete all related details.')
                ->modalSubmitActionLabel('Yes, permanently delete')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('danger')
                ->before(function () {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();
                    $detailCount = $record->details()->withTrashed()->count();
                    if ($detailCount > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cascade Deletion Warning')
                            ->body("⚠️ This will permanently delete the Nota Dinas and {$detailCount} related detail record(s). This action is IRREVERSIBLE!")
                            ->persistent()
                            ->send();
                    }
                })
                ->action(function () {
                    /** @var NotaDinas $record */
                    $record = $this->getRecord();
                    try {
                        $detailCount = $record->details()->withTrashed()->count();
                        $record->forceDelete(); // Uses our custom method with cascade

                        Notification::make()
                            ->success()
                            ->title('Permanently Deleted')
                            ->body("Nota Dinas and {$detailCount} related details permanently deleted.")
                            ->send();
                    } catch (Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Force Delete Failed')
                            ->body('An error occurred: '.$e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
