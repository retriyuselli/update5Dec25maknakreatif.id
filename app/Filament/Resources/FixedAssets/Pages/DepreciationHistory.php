<?php

namespace App\Filament\Resources\FixedAssets\Pages;

use App\Filament\Resources\FixedAssets\FixedAssetResource;
use App\Models\FixedAsset;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepreciationHistory extends Page
{
    use InteractsWithRecord;

    protected static string $resource = FixedAssetResource::class;

    protected string $view = 'filament.resources.fixed-asset-resource.pages.depreciation-history';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return 'Riwayat Penyusutan - '.$this->record->asset_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_edit')
                ->label('Kembali ke Edit')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('edit', ['record' => $this->record])),

            Action::make('generate_depreciation')
                ->label('Generate Penyusutan')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->action(function () {
                    $depreciationAmount = $this->record->calculateMonthlyDepreciation();

                    if ($depreciationAmount > 0) {
                        $currentAccumulated = $this->record->accumulated_depreciation;
                        $newAccumulated = $currentAccumulated + $depreciationAmount;
                        $currentBookValue = $this->record->current_book_value;
                        $newBookValue = $this->record->purchase_price - $newAccumulated;

                        // Create AssetDepreciation record
                        $this->record->depreciations()->create([
                            'depreciation_date' => now(),
                            'depreciation_amount' => $depreciationAmount,
                            'accumulated_depreciation_before' => $currentAccumulated,
                            'accumulated_depreciation_after' => $newAccumulated,
                            'book_value_before' => $currentBookValue,
                            'book_value_after' => $newBookValue,
                            'notes' => 'Penyusutan bulanan - '.now()->format('M Y'),
                            'is_adjustment' => false,
                        ]);

                        // Update asset accumulated depreciation
                        $this->record->update([
                            'accumulated_depreciation' => $newAccumulated,
                        ]);
                        $this->record->updateBookValue();

                        // Create journal entry
                        $this->record->createDepreciationJournalEntry($depreciationAmount);

                        $this->redirect(static::getResource()::getUrl('depreciation-history', ['record' => $this->record]));
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Generate Penyusutan Bulanan')
                ->modalDescription('Ini akan membuat entry penyusutan untuk bulan ini. Lanjutkan?')
                ->visible(fn () => $this->record && ! $this->record->isFullyDepreciated()),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->components([
                Section::make('Informasi Aset')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('asset_code')
                                    ->label('Kode Aset')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('asset_name')
                                    ->label('Nama Aset')
                                    ->weight('bold'),

                                TextEntry::make('category')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn ($state) => FixedAsset::CATEGORIES[$state] ?? $state),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextEntry::make('purchase_price')
                                    ->label('Harga Pembelian')
                                    ->money('IDR'),

                                TextEntry::make('accumulated_depreciation')
                                    ->label('Akumulasi Penyusutan')
                                    ->money('IDR')
                                    ->color('warning'),

                                TextEntry::make('current_book_value')
                                    ->label('Nilai Buku')
                                    ->money('IDR')
                                    ->color('success'),

                                TextEntry::make('useful_life_years')
                                    ->label('Masa Manfaat')
                                    ->suffix(' tahun')
                                    ->formatStateUsing(fn ($state, $record) => $state.($record->useful_life_months ? " {$record->useful_life_months} bulan" : '')),
                            ]),
                    ]),

                Section::make('Riwayat Penyusutan')
                    ->description('Entry penyusutan bulanan untuk aset ini')
                    ->schema([
                        RepeatableEntry::make('depreciations')
                            ->label('')
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextEntry::make('depreciation_date')
                                            ->label('Tanggal')
                                            ->date('M Y')
                                            ->weight('bold'),

                                        TextEntry::make('depreciation_amount')
                                            ->label('Jumlah Penyusutan')
                                            ->money('IDR')
                                            ->color('warning'),

                                        TextEntry::make('accumulated_depreciation_after')
                                            ->label('Akumulasi')
                                            ->money('IDR')
                                            ->color('danger'),

                                        TextEntry::make('book_value_after')
                                            ->label('Nilai Buku')
                                            ->money('IDR')
                                            ->color('success'),

                                        TextEntry::make('notes')
                                            ->label('Catatan')
                                            ->default('-')
                                            ->color('gray'),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->visible(fn () => $this->record->depreciations->count() > 0),

                        TextEntry::make('no_depreciation')
                            ->label('')
                            ->default('Tidak ada entry penyusutan. Klik "Generate Penyusutan" untuk membuat entry bulanan.')
                            ->color('gray')
                            ->visible(fn () => $this->record->depreciations->count() === 0),
                    ]),
            ]);
    }
}
