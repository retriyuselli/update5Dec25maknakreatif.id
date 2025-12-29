<?php

namespace App\Filament\Resources\SimulasiProduks;

use App\Enums\MonthEnum;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\SimulasiProduks\Pages\CreateSimulasiProduk;
use App\Filament\Resources\SimulasiProduks\Pages\EditSimulasiProduk;
use App\Filament\Resources\SimulasiProduks\Pages\ListSimulasiProduks;
use App\Filament\Resources\SimulasiProduks\Pages\ViewSimulasiInvoice;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\SimulasiProduk;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SimulasiProdukResource extends Resource
{
    protected static ?string $model = SimulasiProduk::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Simulasi';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Detail Simulasi')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Simulation Details')
                                ->icon('heroicon-o-identification')
                                ->schema([
                                    Select::make('prospect_id')
                                        ->relationship(
                                            name: 'prospect',
                                            titleAttribute: 'name_event',
                                            modifyQueryUsing: fn (Builder $query, ?SimulasiProduk $record) => $query->whereDoesntHave('orders', function (Builder $orderQuery) {
                                                $orderQuery->whereNotNull('status'); // Hanya prospek yang TIDAK memiliki order dengan status apapun
                                            })->when($record, fn ($q) => $q->orWhere('id', $record->prospect_id)),
                                        )
                                        ->label('Select Prospect (for Simulation Name & Slug)')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, ?string $state) {
                                            if ($state) {
                                                $prospect = Prospect::find($state);
                                                if ($prospect && isset($prospect->name_event)) {
                                                    $set('name', $prospect->name_event); // Set the hidden 'name' field
                                                    $set('slug', Str::slug($prospect->name_event));
                                                } else {
                                                    $set('name', null);
                                                    $set('slug', null);
                                                }
                                            } else {
                                                $set('name', null);
                                                $set('slug', null);
                                            }
                                        })
                                        ->columnSpanFull(),
                                    TextInput::make('name_ttd')
                                        ->label('Name TTD')
                                        ->maxLength(255),
                                    TextInput::make('title_ttd')
                                        ->label('Title TTD')
                                        ->maxLength(255),
                                    Hidden::make('name')
                                        ->dehydrated(), // To store the name derived from prospect
                                    TextInput::make('slug')
                                        ->required()
                                        ->maxLength(255)
                                        ->disabled()
                                        ->dehydrated()
                                        ->unique(SimulasiProduk::class, 'slug', ignoreRecord: true),
                                    RichEditor::make('notes')
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),

                            Section::make('Product & Pricing')
                                ->icon('heroicon-o-shopping-bag')
                                ->columns(2)
                                ->schema([
                                    Select::make('product_id')
                                        ->relationship('product', 'name')
                                        ->label('Select Base Product')
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            $new_total_price = 0;
                                            if ($state) {
                                                $product = Product::find($state);
                                                if ($product) {
                                                    $new_total_price = $product->price ?? 0;
                                                }
                                            }
                                            $set('total_price', $new_total_price);
                                            static::recalculateGrandTotal($get, $set);
                                        })
                                        ->columnSpanFull()
                                        ->suffixAction(
                                            Action::make('openSelectedProduct')
                                                ->icon('heroicon-m-arrow-top-right-on-square')
                                                ->tooltip('Open selected product in new tab')
                                                ->url(function (Get $get): ?string {
                                                    $productId = $get('product_id');
                                                    if (! $productId) {
                                                        return null;
                                                    }
                                                    $product = Product::find($productId);

                                                    return $product ? ProductResource::getUrl('edit', ['record' => $product]) : null;
                                                }, shouldOpenInNewTab: true)
                                                ->hidden(fn (Get $get): bool => blank($get('product_id'))))
                                        ->columnSpanFull(),
                                    TextInput::make('total_price')
                                        ->label('Base Total Price')
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->readOnly()
                                        ->dehydrated()
                                        ->default(0)
                                        ->reactive()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            static::recalculateGrandTotal($get, $set);
                                        })
                                        ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ','))
                                        ->helperText('Price from selected base product. Adjustments below.'),
                                    TextInput::make('promo')
                                        ->label('Potongan Harga (Promo)')
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->default(0)
                                        ->reactive()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculateGrandTotal($get, $set))
                                        ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                                    TextInput::make('penambahan')
                                        ->label('Penambahan Biaya')
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->default(0)
                                        ->reactive()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculateGrandTotal($get, $set))
                                        ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                                    TextInput::make('pengurangan')
                                        ->label('Pengurangan Lain')
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->default(0)
                                        ->reactive()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Get $get, Set $set) => static::recalculateGrandTotal($get, $set))
                                        ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                                    TextInput::make('grand_total')
                                        ->label('Grand Total')
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->readOnly()
                                        ->dehydrated()
                                        ->default(0)
                                        ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                                ])
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Pola Pembayaran')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            TextInput::make('grand_total_display')
                                ->label('Nilai Paket')
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated(false)
                                ->formatStateUsing(fn (Get $get) => number_format(static::parseCurrency($get('grand_total')), 0, '.', ',')),
                            TextInput::make('payment_dp_amount')
                                ->label('Down Payment (DP)')
                                ->prefix('Rp')
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->default(0)
                                ->live(onBlur: true)
                                ->default(0)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    $dp = static::parseCurrency($state);
                                    $items = $get('payment_simulation') ?? [];
                                    $total = $dp;
                                    foreach ($items as $item) {
                                        $total += static::parseCurrency($item['nominal'] ?? 0);
                                    }
                                    $set('total_simulation', $total);
                                })
                                ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                            Repeater::make('payment_simulation')
                                ->label('Simulasi Pembayaran')
                                ->schema([
                                    TextInput::make('persen')
                                        ->label('Persen (%)')
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(100)
                                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                            $grandTotal = static::parseCurrency($get('../../grand_total'));
                                            $dp = static::parseCurrency($get('../../payment_dp_amount'));
                                            $remaining = $grandTotal - $dp;
                                            if ($remaining > 0) {
                                                $nominal = $remaining * ($state / 100);
                                                $set('nominal', $nominal);
                                                $total = $dp;
                                                $items = $get('../../payment_simulation') ?? [];
                                                foreach ($items as $item) {
                                                     $total += static::parseCurrency($item['nominal'] ?? 0);
                                                }
                                                 $set('../../total_simulation', $total);
                                             }
                                         }),
                                    TextInput::make('nominal')
                                        ->label('Nominal')
                                        ->prefix('Rp')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        // ->reactive()
                                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                            $grandTotal = static::parseCurrency($get('../../grand_total'));
                                            $dp = static::parseCurrency($get('../../payment_dp_amount'));
                                            $remaining = $grandTotal - $dp;
                                            $nominalVal = static::parseCurrency($state);
                                            if ($remaining > 0) {
                                                    $persen = ($nominalVal / $remaining) * 100;
                                                    $set('persen', number_format($persen, 2));
                                            }
                                            $items = $get('../../payment_simulation') ?? [];
                                            $total = $dp;
                                            foreach ($items as $item) {
                                                $total += static::parseCurrency($item['nominal'] ?? 0);
                                            }
                                             $set('../../total_simulation', $total);
                                         })
                                         ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                                    Select::make('bulan')
                                        ->label('Bulan / Termin')
                                        ->options(MonthEnum::class),
                                ])
                                ->columns(3)
                                ->columnSpanFull(),
                            TextInput::make('total_simulation')
                                ->label('Total Pembayaran (DP + Termin)')
                                ->prefix('Rp')
                                ->disabled()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->dehydrated()
                                ->default(0)
                                ->rules([
                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $grandTotal = static::parseCurrency($get('grand_total'));
                                        $currentTotal = static::parseCurrency($value);
                                        if (abs($grandTotal - $currentTotal) > 1000) {
                                            $fail('Total Pembayaran (DP + Termin) tidak sama dengan Grand Total (Nilai Paket). Selisih: ' . number_format($grandTotal - $currentTotal, 0, '.', ','));
                                        }
                                    },
                                ])
                                ->formatStateUsing(fn ($state) => number_format((float)$state, 0, '.', ',')),
                        ]),
                    Tab::make('Meta Info')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->label('Created By')
                                ->required()
                                ->searchable()
                                ->disabled()
                                ->preload()
                                ->default(fn () => Auth::id())
                                ->dehydrated(),
                            TextInput::make('created_at_display')
                                ->label('Dibuat')
                                ->disabled()
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($component, $state, ?SimulasiProduk $record): void {
                                    $component->state($record?->created_at?->diffForHumans());
                                }),
                            TextInput::make('updated_at_display')
                                ->label('Terakhir Diubah')
                                ->disabled()
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($component, $state, ?SimulasiProduk $record): void {
                                    $component->state($record?->updated_at?->diffForHumans());
                                }),
                            TextInput::make('last_edited_by_display')
                                ->label('Terakhir Diedit Oleh')
                                ->disabled()
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($component, $state, ?SimulasiProduk $record): void {
                                    $component->state($record?->lastEditedBy?->name ?? '-');
                                }),
                        ])
                        ->hidden(fn (?SimulasiProduk $record) => $record === null),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc') // Tambahkan baris ini
            ->poll('5s') // refresh data setiap 3 detik
            ->columns([
                TextColumn::make('prospect.name_event')
                    ->label('Prospect Name')
                    ->searchable()->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->description(fn (SimulasiProduk $record): string => $record->product ?  ''.$record
                        ->product->name : Str::limit($record->notes ?? '', 30)),
                TextColumn::make('total_price')
                    ->label('Base Price')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('promo')
                    ->money('IDR')  
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
                TextColumn::make('penambahan')
                    ->label('Addition')
                    ->money('IDR')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignEnd(),
                TextColumn::make('pengurangan')
                    ->label('Reduction')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)->alignEnd(),
                TextColumn::make('grand_total')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([ActionGroup::make([
                EditAction::make(),
                Action::make('view_simulasi')
                    ->label('View Simulasi')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn (SimulasiProduk $record) => route('simulasi.show', $record))
                    ->openUrlInNewTab(),
                DeleteAction::make()]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()]),
            ])
            ->emptyStateHeading('No Simulations Found')
            ->emptyStateDescription('Create your first simulation to get started.')
            ->emptyStateIcon('heroicon-o-calculator')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create Simulation')
                    ->icon('heroicon-m-plus')
                    ->url(route('filament.admin.resources.simulasi-produks.create'))
                    ->button(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->poll('60s'); // Refresh data every 60 seconds
    }

    public static function parseCurrency($value): float
    {
        if (empty($value)) return 0;
        // Format: 83,350,000 (US Standard / International)
        // Remove commas (thousands separator)
        $clean = str_replace(',', '', $value);
        return (float) $clean;
    }

    public static function recalculateGrandTotal(Get $get, Set $set, string $basePath = ''): void
    {
        $total_price = static::parseCurrency($get($basePath.'total_price'));
        $promo = static::parseCurrency($get($basePath.'promo'));
        $penambahan = static::parseCurrency($get($basePath.'penambahan'));
        $pengurangan = static::parseCurrency($get($basePath.'pengurangan'));

        $grand_total = $total_price + $penambahan - $promo - $pengurangan;
        $set($basePath.'grand_total', $grand_total);
        $set($basePath.'grand_total_display', number_format($grand_total, 0, '.', ','));
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
            'index' => ListSimulasiProduks::route('/'),
            'create' => CreateSimulasiProduk::route('/create'),
            'edit' => EditSimulasiProduk::route('/{record}/edit'),
            'invoice' => ViewSimulasiInvoice::route('/{record}/invoice'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Example: If you want to scope simulations to the logged-in user (and admins see all)
        // if (!auth()->user()->hasRole('super_admin')) {
        //     return parent::getEloquentQuery()->where('user_id', auth()->id());
        // }
        return parent::getEloquentQuery();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total simulasi yang sedang diproses';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
