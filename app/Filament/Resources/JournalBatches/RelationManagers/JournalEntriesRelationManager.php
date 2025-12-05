<?php

namespace App\Filament\Resources\JournalBatches\RelationManagers;

use App\Models\ChartOfAccount;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JournalEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'journalEntries';

    protected static ?string $title = 'Entri Jurnal';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('account_id')
                            ->label('Akun')
                            ->relationship('chartOfAccount', 'account_name')
                            ->getOptionLabelFromRecordUsing(fn (ChartOfAccount $record): string => "{$record->account_code} - {$record->account_name}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $account = ChartOfAccount::find($state);
                                    if ($account && in_array($account->normal_balance, ['DEBIT'])) {
                                        // For debit normal accounts, default to debit entry
                                        $set('entry_type', 'debit');
                                    } elseif ($account && in_array($account->normal_balance, ['KREDIT'])) {
                                        // For credit normal accounts, default to credit entry
                                        $set('entry_type', 'credit');
                                    }
                                }
                            }),

                        Select::make('entry_type')
                            ->label('Jenis Entry')
                            ->options([
                                'debit' => 'Debit',
                                'credit' => 'Kredit',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Reset amount when switching between debit/credit
                                if ($state === 'debit') {
                                    $set('credit_amount', 0);
                                } else {
                                    $set('debit_amount', 0);
                                }
                            }),
                    ]),

                Textarea::make('description')
                    ->label('Keterangan')
                    ->required()
                    ->rows(2)
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        TextInput::make('debit_amount')
                            ->label('Jumlah Debit')
                            ->numeric()
                            ->prefix('IDR')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->default(0)
                            ->live()
                            ->disabled(fn (callable $get) => $get('entry_type') === 'credit'),

                        TextInput::make('credit_amount')
                            ->label('Jumlah Kredit')
                            ->numeric()
                            ->prefix('IDR')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->default(0)
                            ->live()
                            ->disabled(fn (callable $get) => $get('entry_type') === 'debit'),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('reference_type')
                            ->label('Jenis Referensi')
                            ->maxLength(255),

                        TextInput::make('reference_id')
                            ->label('ID Referensi')
                            ->numeric(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('chartOfAccount.account_code')
                    ->label('Kode Akun')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('chartOfAccount.account_name')
                    ->label('Nama Akun')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('debit_amount')
                    ->label('Debit')
                    ->money('IDR')
                    ->alignEnd()
                    ->color('success')
                    ->getStateUsing(fn ($record) => $record->debit_amount > 0 ? $record->debit_amount : null),

                TextColumn::make('credit_amount')
                    ->label('Kredit')
                    ->money('IDR')
                    ->alignEnd()
                    ->color('info')
                    ->getStateUsing(fn ($record) => $record->credit_amount > 0 ? $record->credit_amount : null),

                TextColumn::make('reference_type')
                    ->label('Referensi')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label('Akun')
                    ->relationship('chartOfAccount', 'account_name')
                    ->getOptionLabelFromRecordUsing(fn (ChartOfAccount $record): string => "{$record->account_code} - {$record->account_name}"),
            ])
            ->headerActions([
                Action::make('show_totals')
                    ->label('Total & Saldo')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->action(function () {
                        $this->ownerRecord->calculateTotals();

                        Notification::make()
                            ->title('Total Jurnal')
                            ->body(sprintf(
                                "Total Debit: Rp %s\nTotal Kredit: Rp %s\nStatus: %s",
                                number_format($this->ownerRecord->total_debit, 0, ',', '.'),
                                number_format($this->ownerRecord->total_credit, 0, ',', '.'),
                                $this->ownerRecord->isBalanced() ? 'SEIMBANG ✅' : 'TIDAK SEIMBANG ❌'
                            ))
                            ->color($this->ownerRecord->isBalanced() ? 'success' : 'warning')
                            ->duration(5000)
                            ->send();
                    }),

                CreateAction::make()
                    ->label('Tambah Entry')
                    ->mutateDataUsing(function (array $data): array {
                        // Set the other amount to 0 based on entry type
                        if (isset($data['entry_type'])) {
                            if ($data['entry_type'] === 'debit') {
                                $data['debit_amount'] = $data['debit_amount'] ?? 0;
                                $data['credit_amount'] = 0;
                            } else {
                                $data['credit_amount'] = $data['credit_amount'] ?? 0;
                                $data['debit_amount'] = 0;
                            }
                            unset($data['entry_type']); // Remove helper field
                        }

                        // Set transaction date from parent batch
                        $data['transaction_date'] = $this->ownerRecord->transaction_date;
                        $data['created_by'] = 1; // Default user for now

                        return $data;
                    })
                    ->after(function () {
                        // Recalculate batch totals after adding entry
                        $this->ownerRecord->calculateTotals();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->fillForm(function ($record): array {
                        $data = $record->toArray();
                        // Set entry_type based on current amounts for editing
                        $data['entry_type'] = $record->debit_amount > 0 ? 'debit' : 'credit';

                        return $data;
                    })
                    ->mutateDataUsing(function (array $data): array {
                        // Set the other amount to 0 based on entry type
                        if (isset($data['entry_type'])) {
                            if ($data['entry_type'] === 'debit') {
                                $data['debit_amount'] = $data['debit_amount'] ?? 0;
                                $data['credit_amount'] = 0;
                            } else {
                                $data['credit_amount'] = $data['credit_amount'] ?? 0;
                                $data['debit_amount'] = 0;
                            }
                            unset($data['entry_type']); // Remove helper field
                        }

                        return $data;
                    })
                    ->after(function () {
                        // Recalculate batch totals after editing
                        $this->ownerRecord->calculateTotals();
                    }),

                DeleteAction::make()
                    ->after(function () {
                        // Recalculate batch totals after deleting
                        $this->ownerRecord->calculateTotals();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function () {
                            // Recalculate batch totals after bulk delete
                            $this->ownerRecord->calculateTotals();
                        }),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Tambah Entry Pertama')
                    ->icon('heroicon-o-plus'),
            ]);
    }
}
