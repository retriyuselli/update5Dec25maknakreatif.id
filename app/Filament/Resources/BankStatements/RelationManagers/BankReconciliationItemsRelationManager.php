<?php

namespace App\Filament\Resources\BankStatements\RelationManagers;

use App\Models\BankReconciliationItem;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BankReconciliationItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'reconciliationItems';

    protected static ?string $model = BankReconciliationItem::class;

    protected static ?string $title = 'Data Rekonsiliasi';

    protected static ?string $modelLabel = 'Item Rekonsiliasi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->native(false),

                Textarea::make('description')
                    ->label('Keterangan')
                    ->required()
                    ->maxLength(500)
                    ->rows(3)
                    ->formatStateUsing(function (?string $state): ?string {
                        // Clean excessive whitespace when loading data into form
                        if (! $state) {
                            return $state;
                        }

                        return preg_replace('/\s+/', ' ', trim($state));
                    })
                    ->dehydrateStateUsing(function (?string $state): ?string {
                        // Clean excessive whitespace when saving
                        if (! $state) {
                            return $state;
                        }

                        return preg_replace('/\s+/', ' ', trim($state));
                    }),

                Select::make('transaction_direction')
                    ->label('Jenis Transaksi')
                    ->options([
                        'masuk' => 'Uang Masuk (Penerimaan)',
                        'keluar' => 'Uang Keluar (Pengeluaran)',
                    ])
                    ->required()
                    ->helperText('Pilih jenis transaksi: Masuk untuk penerimaan, Keluar untuk pengeluaran')
                    ->afterStateHydrated(function (Select $component, ?Model $record) {
                        if ($record && ($record->debit > 0 || $record->credit > 0)) {
                            $component->state($record->debit > 0 ? 'keluar' : 'masuk');
                        }
                    }),

                TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01)
                    ->minValue(0)
                    ->required()
                    ->helperText('Masukkan nominal transaksi')
                    ->afterStateHydrated(function (TextInput $component, ?Model $record) {
                        if ($record) {
                            $amount = $record->debit > 0 ? $record->debit : $record->credit;
                            $component->state($amount);
                        }
                    })
                    ->dehydrated(false), // Don't save this field directly

                // Hidden fields for actual debit/credit values
                Hidden::make('debit')
                    ->dehydrateStateUsing(function ($state, callable $get) {
                        $direction = $get('transaction_direction');
                        $amount = $get('amount');

                        return ($direction === 'keluar') ? floatval($amount) : 0;
                    }),

                Hidden::make('credit')
                    ->dehydrateStateUsing(function ($state, callable $get) {
                        $direction = $get('transaction_direction');
                        $amount = $get('amount');

                        return ($direction === 'masuk') ? floatval($amount) : 0;
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('row_number')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->formatStateUsing(function (string $state): string {
                        // Clean excessive whitespace for table display
                        return preg_replace('/\s+/', ' ', trim($state));
                    }),

                // Optional: Show original debit/credit for technical users
                TextColumn::make('debit')
                    ->label('Debit (Bank)')
                    ->money('IDR')
                    ->alignEnd()
                    ->color('danger')
                    ->sortable()
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Format standar bank: Debit = Uang Keluar'),

                TextColumn::make('credit')
                    ->label('Credit (Bank)')
                    ->money('IDR')
                    ->alignEnd()
                    ->color('success')
                    ->sortable()
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Format standar bank: Credit = Uang Masuk'),
            ])
            ->filters([
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Item')
                    ->visible(function (): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return $user && $user->hasRole('super_admin');
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(function (): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return $user && $user->hasRole('super_admin');
                    }),
                DeleteAction::make()
                    ->visible(function (): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return $user && $user->hasRole('super_admin');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function (): bool {
                            /** @var User $user */
                            $user = Auth::user();

                            return $user && $user->hasRole('super_admin');
                        }),
                ])->visible(function (): bool {
                    /** @var User $user */
                    $user = Auth::user();

                    return $user && $user->hasRole('super_admin');
                }),
            ])
            ->defaultSort('row_number')
            ->emptyStateHeading('Belum ada data rekonsiliasi')
            ->emptyStateDescription('Upload file Excel atau tambah item rekonsiliasi secara manual.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
