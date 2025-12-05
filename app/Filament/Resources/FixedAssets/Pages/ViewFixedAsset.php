<?php

namespace App\Filament\Resources\FixedAssets\Pages;

use App\Filament\Resources\FixedAssets\FixedAssetResource;
use App\Models\FixedAsset;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewFixedAsset extends ViewRecord
{
    protected static string $resource = FixedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('calculate_depreciation')
                ->label('Calculate Depreciation')
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->action(function () {
                    if (! $this->record->isFullyDepreciated()) {
                        $monthlyDepreciation = $this->record->calculateMonthlyDepreciation();
                        $this->record->accumulated_depreciation += $monthlyDepreciation;
                        $this->record->updateBookValue();

                        Notification::make()
                            ->title('Depreciation Calculated')
                            ->body('Monthly depreciation: IDR '.number_format($monthlyDepreciation))
                            ->success()
                            ->send();

                        $this->refreshFormData(['accumulated_depreciation', 'current_book_value']);
                    }
                })
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->isFullyDepreciated()),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $infolist
            ->schema([
                Section::make('Asset Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('asset_code')
                                    ->label('Asset Code')
                                    ->copyable(),

                                TextEntry::make('category')
                                    ->label('Category')
                                    ->formatStateUsing(fn ($state) => FixedAsset::CATEGORIES[$state] ?? $state)
                                    ->badge(),

                                TextEntry::make('condition')
                                    ->label('Condition')
                                    ->formatStateUsing(fn ($state) => FixedAsset::CONDITIONS[$state] ?? $state)
                                    ->badge(),
                            ]),

                        TextEntry::make('asset_name')
                            ->label('Asset Name')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('location')
                                    ->label('Location'),

                                IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean(),
                            ]),
                    ]),

                Section::make('Purchase Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('purchase_date')
                                    ->label('Purchase Date')
                                    ->date('d M Y'),

                                TextEntry::make('purchase_price')
                                    ->label('Purchase Price')
                                    ->money('IDR'),

                                TextEntry::make('salvage_value')
                                    ->label('Salvage Value')
                                    ->money('IDR'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('supplier')
                                    ->label('Supplier'),

                                TextEntry::make('invoice_number')
                                    ->label('Invoice Number'),

                                TextEntry::make('warranty_expiry')
                                    ->label('Warranty Expiry')
                                    ->date('d M Y'),
                            ]),
                    ]),

                Section::make('Depreciation Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('depreciation_method')
                                    ->label('Method')
                                    ->formatStateUsing(fn ($state) => FixedAsset::DEPRECIATION_METHODS[$state] ?? $state),

                                TextEntry::make('useful_life_years')
                                    ->label('Useful Life (Years)')
                                    ->suffix(' years'),

                                TextEntry::make('useful_life_months')
                                    ->label('Additional Months')
                                    ->suffix(' months'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('chartOfAccount.account_name')
                                    ->label('Asset Account')
                                    ->formatStateUsing(fn ($record) => $record->chartOfAccount ?
                                        "{$record->chartOfAccount->account_code} - {$record->chartOfAccount->account_name}" :
                                        'Not set'),

                                TextEntry::make('depreciationAccount.account_name')
                                    ->label('Depreciation Account')
                                    ->formatStateUsing(fn ($record) => $record->depreciationAccount ?
                                        "{$record->depreciationAccount->account_code} - {$record->depreciationAccount->account_name}" :
                                        'Not set'),
                            ]),
                    ]),

                Section::make('Current Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('accumulated_depreciation')
                                    ->label('Accumulated Depreciation')
                                    ->money('IDR'),

                                TextEntry::make('current_book_value')
                                    ->label('Current Book Value')
                                    ->money('IDR')
                                    ->color(fn ($record) => $record->current_book_value <= $record->salvage_value ? 'danger' : 'success'),

                                TextEntry::make('monthly_depreciation')
                                    ->label('Monthly Depreciation')
                                    ->state(fn ($record) => $record->calculateMonthlyDepreciation())
                                    ->money('IDR'),
                            ]),

                        TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),

                Section::make('Depreciation History')
                    ->schema([
                        RepeatableEntry::make('depreciations')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('depreciation_date')
                                            ->label('Date')
                                            ->date('d M Y'),

                                        TextEntry::make('depreciation_amount')
                                            ->label('Amount')
                                            ->money('IDR'),

                                        TextEntry::make('book_value_after')
                                            ->label('Book Value After')
                                            ->money('IDR'),

                                        TextEntry::make('notes')
                                            ->label('Notes'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->depreciations()->exists()),
            ]);
    }
}
