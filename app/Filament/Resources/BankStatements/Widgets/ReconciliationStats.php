<?php

namespace App\Filament\Resources\BankStatements\Widgets;

use App\Models\BankStatement;
use App\Services\ReconciliationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class ReconciliationStats extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record || ! ($this->record instanceof BankStatement)) {
            return [];
        }

        $reconciliationService = new ReconciliationService;
        
        // Use getStoredMatches for performance as it uses DB queries
        $results = $reconciliationService->getStoredMatches(
            $this->record->payment_method_id,
            $this->record->period_start->format('Y-m-d'),
            $this->record->period_end->format('Y-m-d')
        );

        $statistics = $results['statistics'];

        return [
            Stat::make('Transaksi Cocok', $statistics['matched_count'])
                ->description($statistics['match_percentage'] . '% dari total app')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Transaksi App Belum Cocok', $statistics['unmatched_app_count'])
                ->description('Dari ' . $statistics['total_app_transactions'] . ' total transaksi')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make('Mutasi Bank Belum Cocok', $statistics['unmatched_bank_count'])
                ->description('Dari ' . $statistics['total_bank_items'] . ' total mutasi')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Tingkat Kecocokan', $statistics['match_percentage'] . '%')
                ->description('Match Rate Overall')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('primary'),
        ];
    }
}
