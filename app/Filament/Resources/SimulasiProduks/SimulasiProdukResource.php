<?php

namespace App\Filament\Resources\SimulasiProduks;

use App\Filament\Resources\Products\ProductResource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SimulasiProduks\Pages\ListSimulasiProduks;
use App\Filament\Resources\SimulasiProduks\Pages\CreateSimulasiProduk;
use App\Filament\Resources\SimulasiProduks\Pages\EditSimulasiProduk;
use App\Filament\Resources\SimulasiProduks\Pages\ViewSimulasiInvoice;
use App\Filament\Resources\SimulasiProdukResource\Pages;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\SimulasiProduk;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SimulasiProdukResource extends Resource
{
    protected static ?string $model = SimulasiProduk::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-beaker';

    protected static string | \UnitEnum | null $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Simulasi';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Simulation Details')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Select::make('prospect_id')
                            ->relationship(
                                name: 'prospect',
                                titleAttribute: 'name_event',
                                modifyQueryUsing: fn (Builder $query) => $query->whereDoesntHave('orders', function (Builder $orderQuery) {
                                    $orderQuery->whereNotNull('status'); // Hanya prospek yang TIDAK memiliki order dengan status apapun
                                }),
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
                        Hidden::make('name')->dehydrated(), // To store the name derived from prospect
                        TextInput::make('slug')->required()->maxLength(255)->disabled()->dehydrated()->unique(SimulasiProduk::class, 'slug', ignoreRecord: true),
                        Select::make('user_id')->relationship('user', 'name')->label('Created By')->required()->searchable()->disabled()->preload()->default(fn () => Auth::id())->dehydrated(),
                        RichEditor::make('notes')->columnSpanFull(),
                    ])
                    ->columns(2),
                Step::make('Product & Pricing')
                    ->icon('heroicon-o-shopping-bag')
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
                                self::recalculateGrandTotal($get, $set, '../'); // Path to Summary step from Product step
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
                            ->numeric()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::recalculateGrandTotal($get, $set, '../'); // Path to Summary step
                            })
                            ->helperText('Price from selected base product. Adjustments below.'),
                    ]), // End of Product & Pricing Step's schema
                Step::make('Riwayat Modifikasi')
                    ->icon('heroicon-o-clock')
                    ->description('Catat detail modifikasi')
                    ->schema([
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
                                $component->state($record?->user?->name ?? '-');
                            }),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?SimulasiProduk $record) => $record === null),
            ])
                ->columnSpan('full')
                ->columns(3)
                ->skippable(),
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
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->description(fn (SimulasiProduk $record): string => $record->product ? 'Based on: '.$record
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

    protected static function recalculateGrandTotal(Get $get, Set $set, string $basePath = ''): void
    {
        $total_price = floatval(str_replace(',', '', $get($basePath.'total_price') ?? '0'));
        $promo = floatval(str_replace(',', '', $get($basePath.'promo') ?? '0'));
        $penambahan = floatval(str_replace(',', '', $get($basePath.'penambahan') ?? '0'));
        $pengurangan = floatval(str_replace(',', '', $get($basePath.'pengurangan') ?? '0'));

        $grand_total = $total_price + $penambahan - $promo - $pengurangan;
        $set($basePath.'grand_total', $grand_total);
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
