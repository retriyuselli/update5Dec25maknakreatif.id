<?php

namespace App\Filament\Resources\NotaDinasDetails\Pages;

use App\Filament\Resources\NotaDinasDetails\NotaDinasDetailResource;
use App\Models\NotaDinasDetail;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EditNotaDinasDetail extends EditRecord
{
    protected static string $resource = NotaDinasDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Detail Nota Dinas')
                ->modalDescription('Apakah Anda yakin ingin menghapus detail nota dinas ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, hapus')
                ->modalIcon('heroicon-o-exclamation-triangle')
                ->modalIconColor('danger')
                ->visible(function (): bool {
                    /** @var NotaDinasDetail $record */
                    $record = $this->getRecord();

                    // Hide delete button if detail has expense relations
                    if (! $record) {
                        return false;
                    }

                    $hasExpenseRelations = $record->expenses()->exists() ||
                                         $record->expenseOps()->exists() ||
                                         $record->pengeluaranLains()->exists();

                    if ($hasExpenseRelations) {
                        return false;
                    }

                    // Show delete button only for details without expense relations
                    return true;
                })
                ->before(function () {
                    /** @var NotaDinasDetail $record */
                    $record = $this->getRecord();

                    // Validate record exists before showing confirmation
                    if (! $record) {
                        Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body('Data detail nota dinas tidak ditemukan. Silakan refresh halaman dan coba lagi.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Show loading notification
                    Notification::make()
                        ->info()
                        ->title('Memproses')
                        ->body('Memvalidasi detail nota dinas untuk penghapusan...')
                        ->send();
                })
                ->action(function () {
                    /** @var NotaDinasDetail $record */
                    $record = $this->getRecord();

                    // Comprehensive null and existence checks
                    if (! $record) {
                        Notification::make()
                            ->danger()
                            ->title('Penghapusan Gagal')
                            ->body('Data detail nota dinas tidak ditemukan. Mungkin sudah dihapus atau dipindahkan.')
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
                            ->title('Penghapusan Gagal')
                            ->body('Tidak dapat mengakses data detail nota dinas. Mungkin telah dihapus oleh user lain.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Double check for associated expense records
                    $expenseRelations = [];
                    if ($record->expenses()->exists()) {
                        $expenseRelations[] = 'Expense (Wedding): '.$record->expenses()->count().' record';
                    }
                    if ($record->expenseOps()->exists()) {
                        $expenseRelations[] = 'Expense Ops: '.$record->expenseOps()->count().' record';
                    }
                    if ($record->pengeluaranLains()->exists()) {
                        $expenseRelations[] = 'Pengeluaran Lain: '.$record->pengeluaranLains()->count().' record';
                    }

                    if (! empty($expenseRelations)) {
                        Notification::make()
                            ->danger()
                            ->title('Penghapusan Tidak Diizinkan')
                            ->body("Detail nota dinas '{$record->keperluan}' tidak dapat dihapus karena memiliki relasi expense: ".implode(', ', $expenseRelations).'. Silakan hapus expense records terlebih dahulu.')
                            ->persistent()
                            ->send();

                        return false;
                    }

                    // Attempt deletion with comprehensive error handling
                    try {
                        $keperluan = $record->keperluan ?? 'Unknown';
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Detail Nota Dinas Berhasil Dihapus')
                            ->body("'{$keperluan}' telah dihapus dari sistem.")
                            ->duration(5000)
                            ->send();

                        // Redirect to index after successful deletion
                        $this->redirect(NotaDinasDetailResource::getUrl('index'));

                    } catch (QueryException $e) {
                        // Handle database-specific errors
                        $errorCode = $e->getCode();
                        if ($errorCode === '23000') {
                            Notification::make()
                                ->danger()
                                ->title('Penghapusan Gagal - Constraint Data')
                                ->body('Detail nota dinas ini tidak dapat dihapus karena direferensikan oleh record lain dalam sistem.')
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Database Error')
                                ->body('Terjadi kesalahan database saat menghapus detail nota dinas. Silakan coba lagi nanti.')
                                ->persistent()
                                ->send();
                        }

                        return false;

                    } catch (ModelNotFoundException $e) {
                        Notification::make()
                            ->warning()
                            ->title('Detail Nota Dinas Sudah Dihapus')
                            ->body('Detail nota dinas ini sepertinya sudah dihapus oleh user lain.')
                            ->send();

                        return false;

                    } catch (Exception $e) {
                        // Log the error for debugging
                        Log::error('NotaDinasDetail deletion failed from edit page', [
                            'nota_dinas_detail_id' => $record->id ?? 'unknown',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        Notification::make()
                            ->danger()
                            ->title('Kesalahan Tidak Terduga')
                            ->body('Terjadi kesalahan tidak terduga saat menghapus detail nota dinas. Administrator sistem telah diberitahu.')
                            ->persistent()
                            ->send();

                        return false;
                    }
                }),

            // Add informational action for details with expense relations
            Action::make('cannot_delete_info')
                ->label('Tidak Dapat Dihapus')
                ->icon('heroicon-m-shield-exclamation')
                ->color('warning')
                ->tooltip('Detail nota dinas ini tidak dapat dihapus karena memiliki relasi expense')
                ->visible(function (): bool {
                    /** @var NotaDinasDetail $record */
                    $record = $this->getRecord();

                    if (! $record) {
                        return false;
                    }

                    // Show this action only for details with expense relations
                    return $record->expenses()->exists() ||
                           $record->expenseOps()->exists() ||
                           $record->pengeluaranLains()->exists();
                })
                ->action(function () {
                    /** @var NotaDinasDetail $record */
                    $record = $this->getRecord();

                    if ($record) {
                        $expenseRelations = [];
                        if ($record->expenses()->exists()) {
                            $expenseRelations[] = 'Expense (Wedding): '.$record->expenses()->count().' record';
                        }
                        if ($record->expenseOps()->exists()) {
                            $expenseRelations[] = 'Expense Ops: '.$record->expenseOps()->count().' record';
                        }
                        if ($record->pengeluaranLains()->exists()) {
                            $expenseRelations[] = 'Pengeluaran Lain: '.$record->pengeluaranLains()->count().' record';
                        }

                        Notification::make()
                            ->warning()
                            ->title('Tidak Dapat Menghapus Detail Nota Dinas')
                            ->body("'{$record->keperluan}' tidak dapat dihapus karena memiliki relasi expense: ".implode(', ', $expenseRelations).'. Silakan hapus expense records terlebih dahulu.')
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
