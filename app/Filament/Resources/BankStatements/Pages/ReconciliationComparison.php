<?php

namespace App\Filament\Resources\BankStatements\Pages;

use App\Filament\Resources\BankStatements\BankStatementResource;
use App\Models\BankStatement;
use App\Services\ReconciliationService;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ReconciliationComparison extends Page
{
    protected static string $resource = BankStatementResource::class;

    protected string $view = 'filament.resources.bank-statement-resource.pages.reconciliation-comparison';

    protected static ?string $title = 'Rekonsiliasi Perbandingan';

    public ?BankStatement $record = null;
    public bool $dataLoaded = true;
    public ?string $errorReason = null;

    public function mount(mixed $record): void
    {
        // \Illuminate\Support\Facades\Log::info('ReconciliationComparison mount started', ['type' => gettype($record)]);

        try {
            if ($record instanceof BankStatement) {
                $recordModel = $record;
                // Ensure relationships are loaded
                if (!$recordModel->relationLoaded('paymentMethod') || !$recordModel->relationLoaded('reconciliationItems')) {
                    $recordModel->load(['paymentMethod', 'reconciliationItems']);
                }
            } else {
                /** @var BankStatement|null $recordModel */
                $recordModel = BankStatement::with('paymentMethod', 'reconciliationItems')->find($record);
            }

            if (!$recordModel) {
                // \Illuminate\Support\Facades\Log::error('ReconciliationComparison: Record not found', ['id' => $record]);
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
            }

            $this->record = $recordModel;
            // \Illuminate\Support\Facades\Log::info('ReconciliationComparison: Record found', ['id' => $recordModel->id]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->errorReason = 'Bank statement tidak ditemukan.';
            Notification::make()
                ->title('Error')
                ->body($this->errorReason)
                ->danger()
                ->send();

            $this->dataLoaded = false;
            return;
        }

        // Verify that the record has payment method and reconciliation items
        if (! $this->record->payment_method_id || ! $this->record->paymentMethod) {
            // \Illuminate\Support\Facades\Log::error('ReconciliationComparison: Invalid Payment Method', ['id' => $this->record->id, 'pm_id' => $this->record->payment_method_id]);
            $this->errorReason = 'Bank statement tidak memiliki payment method yang valid.';
            Notification::make()
                ->title('Error')
                ->body($this->errorReason)
                ->danger()
                ->send();

            $this->dataLoaded = false;
            return;
        }

        if ($this->record->reconciliationItems->count() === 0) {
            // \Illuminate\Support\Facades\Log::error('ReconciliationComparison: No items', ['id' => $this->record->id]);
            $this->errorReason = 'Bank statement tidak memiliki data reconciliation items (0 items).';
            Notification::make()
                ->title('Error')
                ->body($this->errorReason)
                ->danger()
                ->send();

            $this->dataLoaded = false;
            return;
        }

        // Run reconciliation to ensure DB is up to date
        if ($this->dataLoaded) {
            try {
                $reconciliationService = new ReconciliationService;
                $reconciliationService->reconcile(
                    $this->record->payment_method_id,
                    $this->record->period_start->format('Y-m-d'),
                    $this->record->period_end->format('Y-m-d')
                );
            } catch (Exception $e) {
                // Log error but continue, widgets will handle empty states
                // \Illuminate\Support\Facades\Log::error('Reconciliation failed in mount', ['error' => $e->getMessage()]);
            }
        }

        // \Illuminate\Support\Facades\Log::info('ReconciliationComparison: Mount successful');
    }

    public function autoMatch(): void
    {
        try {
            $reconciliationService = new ReconciliationService;
            $results = $reconciliationService->reconcile(
                $this->record->payment_method_id,
                $this->record->period_start->format('Y-m-d'),
                $this->record->period_end->format('Y-m-d')
            );

            // Auto match high confidence items (85%+)
            $matchedCount = 0;
            foreach ($results['matched'] as $match) {
                if ($match['confidence'] >= 85) {
                    $reconciliationService->markAsMatched(
                        $match['app_transaction'],
                        $match['bank_item'],
                        $match['confidence'],
                        $match['match_reasons']
                    );
                    $matchedCount++;
                }
            }

            if ($matchedCount > 0) {
                Notification::make()
                    ->title('Auto Match Berhasil')
                    ->body("{$matchedCount} transaksi telah di-match otomatis.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Tidak Ada Match')
                    ->body('Tidak ada transaksi dengan confidence 85%+ yang dapat di-match.')
                    ->info()
                    ->send();
            }

        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Terjadi kesalahan: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function markAsMatched(string $sourceId, string $sourceTable, string $bankItemId, float $confidence): void
    {
        try {
            $modelClass = match ($sourceTable) {
                'data_pembayarans' => \App\Models\DataPembayaran::class,
                'pendapatan_lains' => \App\Models\PendapatanLain::class,
                'expenses' => \App\Models\Expense::class,
                'expense_ops' => \App\Models\ExpenseOps::class,
                'pengeluaran_lains' => \App\Models\PengeluaranLain::class,
                default => null,
            };

            if (! $modelClass) {
                throw new Exception("Unknown source table: $sourceTable");
            }

            $record = $modelClass::find($sourceId);
            if (! $record) {
                throw new Exception("Record not found");
            }

            $record->update([
                'reconciliation_status' => 'matched',
                'matched_bank_item_id' => $bankItemId,
                'match_confidence' => $confidence,
                'reconciliation_notes' => 'Manual match via UI',
            ]);

            Notification::make()
                ->title('Berhasil di-match')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function unmarkAsMatched(string $sourceId, string $sourceTable, string $bankItemId): void
    {
        try {
            $modelClass = match ($sourceTable) {
                'data_pembayarans' => \App\Models\DataPembayaran::class,
                'pendapatan_lains' => \App\Models\PendapatanLain::class,
                'expenses' => \App\Models\Expense::class,
                'expense_ops' => \App\Models\ExpenseOps::class,
                'pengeluaran_lains' => \App\Models\PengeluaranLain::class,
                default => null,
            };

            if (! $modelClass) {
                throw new Exception("Unknown source table: $sourceTable");
            }

            $record = $modelClass::find($sourceId);
            if (! $record) {
                throw new Exception("Record not found");
            }

            $record->update([
                'reconciliation_status' => 'unmatched',
                'matched_bank_item_id' => null,
                'match_confidence' => null,
                'reconciliation_notes' => null,
            ]);

            Notification::make()
                ->title('Match dibatalkan')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            BankStatementResource::getUrl('index') => 'Bank Statements',
            BankStatementResource::getUrl('view', ['record' => $this->record]) => 'Bank Statement #'.$this->record->id,
            '#' => 'Rekonsiliasi Perbandingan',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('view', ['record' => $this->record])),

            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    Notification::make()
                        ->title('Fitur Segera Hadir')
                        ->body('Export data rekonsiliasi sedang dalam pengembangan.')
                        ->info()
                        ->send();
                }),

            Action::make('auto_match')
                ->label('Auto Match (85%+)')
                ->icon('heroicon-o-bolt')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Auto Match Transaksi')
                ->modalDescription('Sistem akan otomatis menandai semua kecocokan dengan confidence 85% ke atas sebagai matched. Apakah Anda yakin?')
                ->action(function () {
                    $this->autoMatch();
                }),
        ];
    }

    public function getTitle(): string
    {
        return ''.
               ($this->record->paymentMethod ?
                $this->record->paymentMethod->bank_name.' '.$this->record->paymentMethod->no_rekening :
                'Bank Statement #'.$this->record->id);
    }

    public function getSubheading(): string
    {
        return 'Periode: '.$this->record->period_start->format('d M Y').' - '.$this->record->period_end->format('d M Y');
    }
}
