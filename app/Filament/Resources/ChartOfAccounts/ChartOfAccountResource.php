<?php

namespace App\Filament\Resources\ChartOfAccounts;

use App\Filament\Resources\ChartOfAccounts\Pages\CreateChartOfAccount;
use App\Filament\Resources\ChartOfAccounts\Pages\EditChartOfAccount;
use App\Filament\Resources\ChartOfAccounts\Pages\ListChartOfAccounts;
use App\Models\ChartOfAccount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Bagan Akun';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('account_code')
                                    ->label('Kode Akun')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->placeholder('contoh: 110000000')
                                    ->helperText('Kode akun unik'),

                                Select::make('account_type')
                                    ->label('Jenis Akun')
                                    ->options(ChartOfAccount::ACCOUNT_TYPES)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $normalBalance = ChartOfAccount::NORMAL_BALANCE[$state] ?? 'debit';
                                            $set('normal_balance', $normalBalance);
                                        }
                                    }),
                            ]),

                        TextInput::make('account_name')
                            ->label('Nama Akun')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                Select::make('parent_id')
                                    ->label('Akun Induk')
                                    ->relationship(
                                        'parent',
                                        'account_name',
                                        fn (Builder $query) => $query->where('level', '<', 3)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (ChartOfAccount $record): string => "{$record->account_code} - {$record->account_name}")
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                TextInput::make('level')
                                    ->label('Level')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(5),

                                Select::make('normal_balance')
                                    ->label('Saldo Normal')
                                    ->options([
                                        'debit' => 'Debit',
                                        'credit' => 'Kredit',
                                    ])
                                    ->required(),
                            ]),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('account_code')
            ->columns([
                TextColumn::make('account_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('account_name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $prefix = str_repeat('└── ', $record->level - 1);

                        return $prefix.$record->account_name;
                    }),

                TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ChartOfAccount::ACCOUNT_TYPES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'HARTA' => 'success',
                        'KEWAJIBAN' => 'warning',
                        'MODAL' => 'info',
                        'PENDAPATAN' => 'success',
                        'BEBAN_ATAS_PENDAPATAN' => 'danger',
                        'BEBAN_OPERASIONAL' => 'danger',
                        'PENDAPATAN_LAIN' => 'success',
                        'BEBAN_LAIN' => 'danger',
                        default => 'gray'
                    }),

                TextColumn::make('parent.account_name')
                    ->label('Parent')
                    ->placeholder('Main Account'),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge(),

                TextColumn::make('normal_balance')
                    ->label('Normal Balance')
                    ->badge()
                    ->color(fn ($state) => $state === 'debit' ? 'success' : 'warning'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->placeholder('Not deleted')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('dependencies')
                    ->label('Dependencies')
                    ->getStateUsing(function (ChartOfAccount $record): string {
                        $journalCount = DB::table('journal_entries')->where('account_id', $record->id)->count();
                        $childrenCount = ChartOfAccount::where('parent_id', $record->id)->count();

                        $dependencies = [];
                        if ($journalCount > 0) {
                            $dependencies[] = "{$journalCount} journals";
                        }
                        if ($childrenCount > 0) {
                            $dependencies[] = "{$childrenCount} children";
                        }

                        return empty($dependencies) ? '-' : implode(', ', $dependencies);
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        return $state === '-' ? 'success' : 'warning';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('account_type')
                    ->label('Account Type')
                    ->options(ChartOfAccount::ACCOUNT_TYPES),

                SelectFilter::make('level')
                    ->label('Level')
                    ->options([
                        1 => 'Level 1 (Main)',
                        2 => 'Level 2 (Sub)',
                        3 => 'Level 3 (Detail)',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All accounts')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                TrashedFilter::make()
                    ->label('Deleted Status')
                    ->placeholder('Active accounts only')
                    ->trueLabel('With deleted accounts')
                    ->falseLabel('Deleted accounts only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Account')
                    ->modalDescription('This will move the account to trash. If this account has journal entries or child accounts, you can still restore it later.')
                    ->modalSubmitActionLabel('Move to Trash'),
                RestoreAction::make(),
                ForceDeleteAction::make()
                    ->before(function (ForceDeleteAction $action, ChartOfAccount $record) {
                        // Check if account has related journal entries
                        $journalEntriesCount = DB::table('journal_entries')
                            ->where('account_id', $record->id)
                            ->count();

                        if ($journalEntriesCount > 0) {
                            Notification::make()
                                ->title('Cannot Force Delete Account')
                                ->body("This account has {$journalEntriesCount} journal entries. Please delete or reassign them first.")
                                ->danger()
                                ->send();

                            $action->cancel();
                        }

                        // Check if account has child accounts
                        $childrenCount = ChartOfAccount::where('parent_id', $record->id)->count();

                        if ($childrenCount > 0) {
                            Notification::make()
                                ->title('Cannot Force Delete Account')
                                ->body("This account has {$childrenCount} child accounts. Please delete or reassign them first.")
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Force Delete Account')
                    ->modalDescription('Are you sure you want to permanently delete this account? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete permanently'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->before(function (ForceDeleteBulkAction $action, Collection $records) {
                            $blockedAccounts = [];

                            foreach ($records as $record) {
                                // Check journal entries
                                $journalEntriesCount = DB::table('journal_entries')
                                    ->where('account_id', $record->id)
                                    ->count();

                                // Check child accounts
                                $childrenCount = ChartOfAccount::where('parent_id', $record->id)->count();

                                if ($journalEntriesCount > 0 || $childrenCount > 0) {
                                    $blockedAccounts[] = [
                                        'name' => $record->account_name,
                                        'code' => $record->account_code,
                                        'journal_entries' => $journalEntriesCount,
                                        'children' => $childrenCount,
                                    ];
                                }
                            }

                            if (! empty($blockedAccounts)) {
                                $message = "Cannot force delete the following accounts:\n\n";
                                foreach ($blockedAccounts as $account) {
                                    $reasons = [];
                                    if ($account['journal_entries'] > 0) {
                                        $reasons[] = "{$account['journal_entries']} journal entries";
                                    }
                                    if ($account['children'] > 0) {
                                        $reasons[] = "{$account['children']} child accounts";
                                    }
                                    $message .= "• {$account['code']} - {$account['name']} (".implode(', ', $reasons).")\n";
                                }
                                $message .= "\nPlease delete or reassign related records first.";

                                Notification::make()
                                    ->title('Cannot Force Delete Accounts')
                                    ->body($message)
                                    ->danger()
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Force Delete Accounts')
                        ->modalDescription('Are you sure you want to permanently delete these accounts? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete permanently'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChartOfAccounts::route('/'),
            'create' => CreateChartOfAccount::route('/create'),
            'edit' => EditChartOfAccount::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total akun pada bagan akun';
    }
}
