<?php

namespace App\Filament\Resources\JournalBatches\Widgets;

use App\Models\Expense;
use App\Models\JournalBatch;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JournalSystemStatusWidget extends BaseWidget
{
    // protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Count total journal batches
        $totalJournals = JournalBatch::count();

        // Count expense journals
        $expenseJournals = JournalBatch::where('reference_type', 'expense')->count();

        // Count expenses without journals (check both 'expense' and 'expense_reversal' types)
        $expensesWithoutJournals = Expense::whereDoesntHave('journalBatches', function ($query) {
            $query->whereIn('reference_type', ['expense', 'expense_reversal']);
        })->count();

        // Count future dated journals (problematic)
        $futureJournals = JournalBatch::where('transaction_date', '>', now())->count();

        // Count expenses with future dates
        $futureExpenses = Expense::where('date_expense', '>', now())->count();

        // Count expenses after order closing
        $expensesAfterClosing = Expense::whereHas('order', function ($query) {
            $query->whereRaw('expenses.date_expense > orders.closing_date');
        })->count();

        return [
            Stat::make('Total Journal Batches', number_format($totalJournals))
                ->description('Semua entri jurnal dalam sistem')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Expense Journals', number_format($expenseJournals))
                ->description('Entri jurnal untuk expense')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),

            Stat::make('Missing Journals', number_format($expensesWithoutJournals))
                ->description('Expense yang belum memiliki jurnal')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expensesWithoutJournals > 0 ? 'warning' : 'success'),

            Stat::make('Future Journal Dates', number_format($futureJournals))
                ->description('Jurnal dengan tanggal transaksi masa depan')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($futureJournals > 0 ? 'danger' : 'success'),

            Stat::make('Future Expense Dates', number_format($futureExpenses))
                ->description('Expense dengan tanggal masa depan')
                ->descriptionIcon('heroicon-m-clock')
                ->color($futureExpenses > 0 ? 'danger' : 'success'),

            Stat::make('Post-Closing Expenses', number_format($expensesAfterClosing))
                ->description('Expense setelah penutupan order')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($expensesAfterClosing > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }

    public function getDisplayName(): string
    {
        return 'Journal System Status';
    }
}
