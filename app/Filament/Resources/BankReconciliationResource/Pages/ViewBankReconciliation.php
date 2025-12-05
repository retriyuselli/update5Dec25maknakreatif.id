<?php

namespace App\Filament\Resources\BankReconciliationResource\Pages;

use App\Filament\Resources\BankReconciliationResource;
use App\Models\BankReconciliation;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewBankReconciliation extends ViewRecord
{
    protected static string $resource = BankReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('bank-reconciliation.template'))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make('Informasi Rekonsiliasi')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('paymentMethod.name')
                                ->label('Rekening Bank'),
                            TextEntry::make('reconciliation_date')
                                ->label('Tanggal Rekonsiliasi')
                                ->date('d M Y'),
                        ]),

                        Grid::make(2)->schema([
                            TextEntry::make('period_start')
                                ->label('Periode Mulai')
                                ->date('d M Y'),
                            TextEntry::make('period_end')
                                ->label('Periode Selesai')
                                ->date('d M Y'),
                        ]),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'secondary',
                                'pending' => 'warning',
                                'reviewed' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => BankReconciliation::getStatusOptions()[$state] ?? $state),
                    ])->columns(1),

                Section::make('Saldo & Penyesuaian')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('book_balance')
                                ->label('Saldo Buku')
                                ->money('idr'),
                            TextEntry::make('bank_balance')
                                ->label('Saldo Bank')
                                ->money('idr'),
                            TextEntry::make('adjusted_book_balance')
                                ->label('Saldo Buku Disesuaikan')
                                ->money('idr'),
                            TextEntry::make('adjusted_bank_balance')
                                ->label('Saldo Bank Disesuaikan')
                                ->money('idr'),
                        ]),

                        TextEntry::make('difference')
                            ->label('Selisih')
                            ->money('idr')
                            ->color(fn ($state) => $state == 0 ? 'success' : 'danger')
                            ->weight('bold')
                            ->size('lg'),
                    ]),

                Section::make('Statistik Matching')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('matched_transactions_count')
                                ->label('Transaksi Matched')
                                ->getStateUsing(fn ($record) => $record->matched_transactions_count ?? 0)
                                ->suffix(' transaksi')
                                ->color('success'),
                            TextEntry::make('unmatched_bank_transactions_count')
                                ->label('Bank Unmatched')
                                ->getStateUsing(fn ($record) => $record->unmatched_bank_transactions_count ?? 0)
                                ->suffix(' transaksi')
                                ->color('warning'),
                            TextEntry::make('unmatched_book_transactions_count')
                                ->label('Buku Unmatched')
                                ->getStateUsing(fn ($record) => $record->unmatched_book_transactions_count ?? 0)
                                ->suffix(' transaksi')
                                ->color('danger'),
                        ]),
                    ]),

                Section::make('Audit Trail')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('reconciledBy.name')
                                ->label('Dibuat Oleh'),
                            TextEntry::make('reconciled_at')
                                ->label('Tanggal Dibuat')
                                ->dateTime('d M Y H:i'),
                        ]),

                        Grid::make(2)->schema([
                            TextEntry::make('approvedBy.name')
                                ->label('Disetujui Oleh'),
                            TextEntry::make('approved_at')
                                ->label('Tanggal Disetujui')
                                ->dateTime('d M Y H:i'),
                        ]),
                    ])->visible(fn ($record) => $record->reconciled_by || $record->approved_by),

                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->notes)),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            // BankReconciliationResource\Widgets\BankReconciliationItemsTable::class,
        ];
    }
}
