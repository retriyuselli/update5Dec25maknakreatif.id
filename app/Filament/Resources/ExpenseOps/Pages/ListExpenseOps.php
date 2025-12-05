<?php

namespace App\Filament\Resources\ExpenseOps\Pages;

use App\Filament\Resources\ExpenseOps\ExpenseOpsResource;
use App\Filament\Resources\ExpenseOps\Widgets\ExpenseOpsOverview;
use App\Imports\ExpenseOpsImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListExpenseOps extends ListRecords
{
    protected static string $resource = ExpenseOpsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengeluaran Operasional')
                ->icon('heroicon-o-plus'),

            // Actions\ActionGroup::make([
            //     Actions\Action::make('viewHtmlReport')
            //         ->label('Laporan Pengeluaran Operasional')
            //         ->icon('heroicon-o-document-text')
            //         ->url(route('expense-ops.html-report'), true)
            //         ->color('info'),

            //     Actions\Action::make('downloadTemplate')
            //         ->label('Download Template')
            //         ->icon('heroicon-o-document-arrow-down')
            //         ->url(asset('templates/template_import_expense_ops.xlsx'), shouldOpenInNewTab: false)
            //         ->color('gray'),

            //     Actions\Action::make('import')
            //         ->label('Import Expenses')
            //         ->color('success')
            //         ->icon('heroicon-o-arrow-down-tray')
            //         ->form([
            //             FileUpload::make('attachment')
            //                 ->label('Excel File')
            //                 ->disk('local')
            //                 ->directory('expense-imports')
            //                 ->acceptedFileTypes([
            //                     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            //                     'application/vnd.ms-excel'
            //                 ])
            //                 ->maxSize(10240)
            //                 ->required()
            //                 ->helperText('Upload Excel file (.xlsx, .xls) with expense data'),
            //         ])
            //         ->action(function (array $data): void {
            //             try {
            //                 if (!isset($data['attachment']) || !Storage::disk('local')->exists($data['attachment'])) {
            //                     throw new \Exception('Upload file not found. Please try again.');
            //                 }

            //                 $filePath = Storage::disk('local')->path($data['attachment']);

            //                 $import = new ExpenseOpsImport();

            //                 Excel::import($import, $filePath);

            //                 Notification::make()
            //                     ->title('Import Successful')
            //                     ->body(sprintf('Successfully imported %d expenses', $import->getRowCount()))
            //                     ->success()
            //                     ->send();

            //             } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            //                 $failures = $e->failures();
            //                 $errorMessage = collect($failures)
            //                     ->map(fn($failure) => sprintf(
            //                         'Row %d: %s (Value: %s)',
            //                         $failure->row(),
            //                         implode(', ', $failure->errors()),
            //                         $failure->values()[$failure->attribute()] ?? 'N/A'
            //                     ))
            //                     ->implode("\n");

            //                 Notification::make()
            //                     ->title('Import Failed')
            //                     ->body($errorMessage)
            //                     ->danger()
            //                     ->persistent()
            //                     ->send();

            //             } catch (\Exception $e) {
            //                 $errorMessage = sprintf(
            //                     'Import Error: %s. File path: %s',
            //                     $e->getMessage(),
            //                     $data['attachment'] ?? 'not set'
            //                 );

            //                 Notification::make()
            //                     ->title('Import Error')
            //                     ->body($errorMessage)
            //                     ->danger()
            //                     ->persistent()
            //                     ->send();

            //                 logger()->error('Expense import failed', [
            //                     'error' => $e->getMessage(),
            //                     'file_path' => $data['attachment'] ?? 'not set',
            //                     'trace' => $e->getTraceAsString()
            //                 ]);
            //             } finally {
            //                 if (isset($data['attachment']) && Storage::disk('local')->exists($data['attachment'])) {
            //                     Storage::disk('local')->delete($data['attachment']);
            //                 }
            //             }
            //         })
            //         ->tooltip('Import expenses from an Excel file'),
            // ])
            // ->label('Others')
            //     ->button()
            //     ->color('warning'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseOpsOverview::class,
        ];
    }
}
