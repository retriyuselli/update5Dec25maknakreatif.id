<?php

namespace App\Filament\Resources\BankStatements\Widgets;

use App\Models\BankStatement;
use App\Services\ReconciliationService;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class UnmatchedBankTable extends Widget
{
    protected string $view = 'filament.resources.bank-statement-resource.widgets.unmatched-bank-table';

    public ?Model $record = null;

    protected $listeners = ['reconciliation-updated' => '$refresh'];

    protected function getViewData(): array
    {
        if (! $this->record || ! ($this->record instanceof BankStatement)) {
            return ['items' => []];
        }

        $service = new ReconciliationService;
        $results = $service->getStoredMatches(
            $this->record->payment_method_id,
            $this->record->period_start->format('Y-m-d'),
            $this->record->period_end->format('Y-m-d')
        );

        return [
            'items' => $results['unmatched_bank'],
        ];
    }
}
