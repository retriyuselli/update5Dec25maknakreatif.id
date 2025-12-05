<?php

namespace App\Filament\Resources\JournalBatches\Pages;

use App\Filament\Resources\JournalBatches\JournalBatchResource;
use App\Filament\Resources\JournalBatches\Widgets\JournalSystemStatusWidget;
use App\Models\JournalBatch;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListJournalBatches extends ListRecords
{
    protected static string $resource = JournalBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Jurnal Manual Baru')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->modalHeading('Buat Entri Jurnal Manual')
                ->modalDescription('Jurnal manual hanya boleh dibuat untuk penyesuaian, koreksi, transaksi aset, atau entri non-operasional. Sebagian besar jurnal expense/pembayaran dibuat otomatis.')
                ->modalWidth('7xl'),

            Action::make('export_excel')
                ->label('Ekspor ke Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        $journals = JournalBatch::with(['journalEntries'])->get();

                        // Create new Spreadsheet object
                        $spreadsheet = new Spreadsheet;
                        $sheet = $spreadsheet->getActiveSheet();

                        // Set document properties
                        $spreadsheet->getProperties()
                            ->setCreator('Sistem Manajemen Jurnal')
                            ->setTitle('Ekspor Batch Jurnal')
                            ->setSubject('Data Batch Jurnal')
                            ->setDescription('Ekspor semua batch jurnal dari sistem');

                        // Set header row
                        $headers = ['ID', 'Nomor Referensi', 'Tanggal Transaksi', 'Deskripsi', 'Jenis Jurnal', 'Total Debit', 'Total Kredit', 'Dibuat Pada'];
                        $sheet->fromArray([$headers], null, 'A1');

                        // Style header row
                        $headerRange = 'A1:H1';
                        $sheet->getStyle($headerRange)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF'],
                                'size' => 12,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '4472C4'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        // Add data rows
                        $row = 2;
                        foreach ($journals as $journal) {
                            $sheet->setCellValue('A'.$row, $journal->id);
                            $sheet->setCellValue('B'.$row, $journal->reference_number ?? '');
                            $sheet->setCellValue('C'.$row, $journal->transaction_date?->format('Y-m-d') ?? '');
                            $sheet->setCellValue('D'.$row, $journal->description ?? '');
                            $sheet->setCellValue('E'.$row, $journal->journal_type ?? '');
                            $sheet->setCellValue('F'.$row, $journal->total_debit ?? 0);
                            $sheet->setCellValue('G'.$row, $journal->total_credit ?? 0);
                            $sheet->setCellValue('H'.$row, $journal->created_at?->format('Y-m-d H:i:s') ?? '');
                            $row++;
                        }

                        // Auto-size columns
                        foreach (range('A', 'H') as $column) {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
                        }

                        // Format number columns
                        $dataRange = 'F2:G'.($row - 1);
                        $sheet->getStyle($dataRange)->getNumberFormat()->setFormatCode('#,##0.00');

                        // Add borders
                        $allDataRange = 'A1:H'.($row - 1);
                        $sheet->getStyle($allDataRange)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ]);

                        // Generate filename
                        $filename = 'batch-jurnal-'.now()->format('Y-m-d-His').'.xlsx';

                        // Create writer and save to temporary file
                        $writer = new Xlsx($spreadsheet);
                        $tempFile = tempnam(sys_get_temp_dir(), 'ekspor_jurnal_');
                        $writer->save($tempFile);

                        // Return download response
                        return response()->download($tempFile, $filename, [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])->deleteFileAfterSend(true);

                    } catch (Exception $e) {
                        Notification::make()
                            ->title('Ekspor Gagal')
                            ->body('Error: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn () => JournalBatch::count() > 0),

            Action::make('preview_pdf')
                ->label('Preview PDF')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->schema([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->displayFormat('d/m/Y'),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->displayFormat('d/m/Y'),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'posted' => 'Posted',
                        ])
                        ->placeholder('All Status'),
                    Select::make('reference_type')
                        ->label('Reference Type')
                        ->options([
                            'expense' => 'Expense',
                            'payment' => 'Payment',
                            'other_income' => 'Other Income',
                            'operational_expense' => 'Operational Expense',
                            'other_expense' => 'Other Expense',
                            'asset_purchase' => 'Asset Purchase',
                            'asset_depreciation' => 'Asset Depreciation',
                            'asset_disposal' => 'Asset Disposal',
                            'manual' => 'Manual',
                        ])
                        ->placeholder('All Types'),
                ])
                ->action(function (array $data) {
                    $url = route('journal.pdf.preview', array_filter($data));

                    return redirect($url);
                })
                ->modalWidth('md')
                ->modalSubmitActionLabel('Generate PDF')
                ->visible(fn () => JournalBatch::count() > 0),

            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->schema([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->displayFormat('d/m/Y'),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->displayFormat('d/m/Y'),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'posted' => 'Posted',
                        ])
                        ->placeholder('All Status'),
                    Select::make('reference_type')
                        ->label('Reference Type')
                        ->options([
                            'expense' => 'Expense',
                            'payment' => 'Payment',
                            'other_income' => 'Other Income',
                            'operational_expense' => 'Operational Expense',
                            'other_expense' => 'Other Expense',
                            'asset_purchase' => 'Asset Purchase',
                            'asset_depreciation' => 'Asset Depreciation',
                            'asset_disposal' => 'Asset Disposal',
                            'manual' => 'Manual',
                        ])
                        ->placeholder('All Types'),
                ])
                ->action(function (array $data) {
                    $url = route('journal.pdf.download', array_filter($data));

                    return redirect($url);
                })
                ->modalWidth('md')
                ->modalSubmitActionLabel('Download PDF')
                ->visible(fn () => JournalBatch::count() > 0),

            ActionGroup::make([
                Action::make('regenerate_missing')
                    ->label('Generate Jurnal yang Hilang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Entri Jurnal yang Hilang')
                    ->modalDescription('Ini akan membuat entri jurnal untuk expense yang belum memiliki jurnal. Proses ini aman dan tidak akan mempengaruhi jurnal yang sudah ada.')
                    ->modalSubmitActionLabel('Generate Jurnal')
                    ->action(function () {
                        try {
                            // Run the regenerate command for missing expense journals with no-interaction flag
                            Artisan::call('journal:regenerate', [
                                '--type' => 'expense',
                                '--no-interaction' => true,
                            ]);

                            $output = Artisan::output();

                            // Parse output to get regenerated count
                            preg_match('/Regenerated: (\d+)/', $output, $matches);
                            $regeneratedCount = $matches[1] ?? 0;

                            Notification::make()
                                ->title('Generate Jurnal Selesai')
                                ->body("Berhasil membuat {$regeneratedCount} entri jurnal yang hilang.")
                                ->success()
                                ->send();

                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Generate Gagal')
                                ->body('Error: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('regenerate_all')
                    ->label('Regenerate Semua Jurnal')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Regenerate Semua Entri Jurnal')
                    ->modalDescription('Ini akan me-regenerate SEMUA entri jurnal dengan tanggal transaksi yang benar. Jurnal yang sudah ada akan diganti. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Paksa Regenerate')
                    ->action(function () {
                        try {
                            // Run the regenerate command with force and no-interaction flags
                            Artisan::call('journal:regenerate', [
                                '--type' => 'all',
                                '--force' => true,
                                '--no-interaction' => true,
                            ]);

                            $output = Artisan::output();

                            Notification::make()
                                ->title('Regenerate Jurnal Selesai')
                                ->body('Semua entri jurnal telah di-regenerate dengan tanggal transaksi yang benar.')
                                ->success()
                                ->send();

                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Regenerate Gagal')
                                ->body('Error: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('fix_expense_dates')
                    ->label('Perbaiki Tanggal Expense')
                    ->icon('heroicon-o-calendar-days')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Perbaiki Tanggal Expense Bermasalah')
                    ->modalDescription('Ini akan memperbaiki expense dengan tanggal masa depan atau tanggal setelah penutupan order. Entri jurnal akan diperbarui sesuai.')
                    ->modalSubmitActionLabel('Perbaiki Tanggal')
                    ->action(function () {
                        try {
                            // Run the fix expense dates command
                            Artisan::call('fix:expense-journal-dates');

                            $output = Artisan::output();

                            // Parse output to get fixed count
                            preg_match_all('/Fixed Expense \d+/', $output, $matches);
                            $fixedCount = count($matches[0] ?? []);

                            Notification::make()
                                ->title('Perbaikan Tanggal Selesai')
                                ->body("Berhasil memperbaiki {$fixedCount} record expense dan entri jurnal mereka.")
                                ->success()
                                ->send();

                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Perbaikan Gagal')
                                ->body('Error: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
                ->label('Manajemen Jurnal')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary')
                ->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            JournalSystemStatusWidget::class,
        ];
    }
}
