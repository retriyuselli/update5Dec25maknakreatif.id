<?php

namespace App\Filament\Resources\BankStatements\Pages;

use App\Filament\Resources\BankStatements\BankStatementResource;
use App\Imports\BankReconciliationImport;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CreateBankStatement extends CreateRecord
{
    protected static string $resource = BankStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template Rekonsiliasi')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('bank-reconciliation.template'))
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set uploaded_by to current user
        $data['uploaded_by'] = Auth::id();

        // Handle reconciliation file
        if (! empty($data['reconciliation_file'])) {
            $reconciliationFilePath = $data['reconciliation_file'];

            // Set reconciliation original filename
            $data['reconciliation_original_filename'] = basename($reconciliationFilePath);

            // Store reconciliation file path in session for processing after create
            session(['pending_reconciliation_file' => $reconciliationFilePath]);
        }

        // Remove reconciliation_file from data as it's not a database field
        unset($data['reconciliation_file']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Check if there's a pending reconciliation file from session
        $reconciliationFile = session('pending_reconciliation_file');

        if ($reconciliationFile) {
            // Check if file is Excel format
            $fileExtension = strtolower(pathinfo($reconciliationFile, PATHINFO_EXTENSION));
            if (! in_array($fileExtension, ['xlsx', 'xls', 'csv'])) {
                Notification::make()
                    ->title('Format File Tidak Didukung')
                    ->body('Hanya file Excel (.xlsx, .xls) atau CSV yang dapat diimpor untuk rekonsiliasi.')
                    ->warning()
                    ->send();

                session()->forget('pending_reconciliation_file');

                return;
            }

            try {
                $record->update(['reconciliation_status' => 'processing']);

                // Use BankStatement as bank reconciliation for the import
                $import = new BankReconciliationImport($record);
                Excel::import($import, storage_path('app/public/'.$reconciliationFile));

                // Check for errors from the import
                $errors = $import->getErrors();
                $importedCount = $import->getImportedCount();

                if (! empty($errors)) {
                    $record->update(['reconciliation_status' => 'failed']);

                    $errorMessage = "Berhasil mengimpor {$importedCount} transaksi, tetapi ada ".count($errors)." error:\n";
                    $errorMessage .= implode("\n", array_slice($errors, 0, 5)); // Show first 5 errors
                    if (count($errors) > 5) {
                        $errorMessage .= "\n... dan ".(count($errors) - 5).' error lainnya';
                    }

                    Notification::make()
                        ->title('Import Rekonsiliasi Selesai dengan Error')
                        ->body($errorMessage)
                        ->warning()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Import Rekonsiliasi Berhasil!')
                        ->body("Berhasil mengimpor {$importedCount} transaksi rekonsiliasi.")
                        ->success()
                        ->send();
                }

            } catch (Exception $e) {
                $record->update(['reconciliation_status' => 'failed']);

                Notification::make()
                    ->title('Import Rekonsiliasi Gagal')
                    ->body('Error: '.$e->getMessage())
                    ->danger()
                    ->send();
            }

            // Clear the session after processing
            session()->forget('pending_reconciliation_file');
        }
    }
}
