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

    public BankStatement $record;

    public function mount(int|string $record): void
    {
        $this->record = BankStatement::with('paymentMethod', 'reconciliationItems')->findOrFail($record);

        // Verify that the record has payment method and reconciliation items
        if (! $this->record->payment_method_id || ! $this->record->paymentMethod) {
            abort(404, 'Bank statement tidak memiliki payment method yang valid.');
        }

        if ($this->record->reconciliationItems()->count() === 0) {
            abort(404, 'Bank statement tidak memiliki data reconciliation items.');
        }
    }

    public function getReconciliationData(): array
    {
        $reconciliationService = new ReconciliationService;

        return $reconciliationService->reconcile(
            $this->record->payment_method_id,
            $this->record->period_start->format('Y-m-d'),
            $this->record->period_end->format('Y-m-d')
        );
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

    public function getBreadcrumbs(): array
    {
        return [
            url()->route('filament.admin.resources.bank-statements.index') => 'Bank Statements',
            url()->route('filament.admin.resources.bank-statements.view', ['record' => $this->record]) => 'Bank Statement #'.$this->record->id,
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
                ->url(function () {
                    return url('/admin/reconciliation/export?'.http_build_query([
                        'payment_method_id' => $this->record->payment_method_id,
                        'start_date' => $this->record->period_start->format('Y-m-d'),
                        'end_date' => $this->record->period_end->format('Y-m-d'),
                    ]));
                })
                ->openUrlInNewTab(),

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
        return 'Rekonsiliasi Perbandingan - '.
               ($this->record->paymentMethod ?
                $this->record->paymentMethod->bank_name.' '.$this->record->paymentMethod->no_rekening :
                'Bank Statement #'.$this->record->id);
    }

    public function getSubheading(): string
    {
        return 'Periode: '.$this->record->period_start->format('d M Y').' - '.$this->record->period_end->format('d M Y');
    }
}
