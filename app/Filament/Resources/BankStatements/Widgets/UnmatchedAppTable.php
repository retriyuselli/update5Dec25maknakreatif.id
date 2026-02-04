<?php

namespace App\Filament\Resources\BankStatements\Widgets;

use App\Services\ReconciliationService;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UnmatchedAppTable extends Widget
{
    protected string $view = 'filament.resources.bank-statement-resource.widgets.unmatched-app-table';
    public ?Model $record = null;
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['reconciliation-updated' => '$refresh'];

    protected function getViewData(): array
    {
        if (! $this->record) {
            return ['items' => []];
        }

        $service = new ReconciliationService;
        $results = $service->getStoredMatches(
            $this->record->payment_method_id,
            $this->record->period_start->format('Y-m-d'),
            $this->record->period_end->format('Y-m-d')
        );

        return [
            'items' => $results['unmatched_app'],
        ];
    }

    public function findManualMatch($sourceId, $sourceTable)
    {
        Notification::make()
            ->title('Fitur Segera Hadir')
            ->body('Fitur manual match sedang dalam pengembangan.')
            ->info()
            ->send();
    }
}
