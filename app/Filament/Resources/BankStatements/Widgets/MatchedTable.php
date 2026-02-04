<?php

namespace App\Filament\Resources\BankStatements\Widgets;

use App\Services\ReconciliationService;
use Exception;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MatchedTable extends Widget
{
    protected string $view = 'filament.resources.bank-statement-resource.widgets.matched-table';
    public ?Model $record = null;
    protected int | string | array $columnSpan = 'full';
    
    protected $listeners = ['reconciliation-updated' => '$refresh'];

    protected function getViewData(): array
    {
        if (! $this->record) {
            return ['matches' => []];
        }

        $service = new ReconciliationService;
        $results = $service->getStoredMatches(
            $this->record->payment_method_id,
            $this->record->period_start->format('Y-m-d'),
            $this->record->period_end->format('Y-m-d')
        );

        return [
            'matches' => $results['matched'],
        ];
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

            $this->dispatch('reconciliation-updated');

        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
