<?php

namespace App\Filament\Resources\Products;

use App\Exports\ProductExport;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Vendors\VendorResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?string $modelLabel = 'Produk';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Details')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->live(onBlur: true)
                                        ->maxLength(255)
                                        ->placeholder('nama pengantin_lokasi_pax')
                                        ->afterStateUpdated(fn (string $state, Set $set) => $set('slug', Str::slug($state))
                                        ),

                                    Hidden::make('slug')
                                        ->disabled()
                                        ->dehydrated()
                                        ->unique(ignoreRecord: true)
                                        ->helperText('Otomatis dibuat dari nama'),

                                    FileUpload::make('image')
                                        ->image()
                                        ->imageEditor()
                                        ->directory('products')
                                        ->downloadable(),

                                    Select::make('category_id')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->required()
                                        ->preload()
                                        ->placeholder('Select a category')
                                        ->createOptionForm([

                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn (string $state, Set $set) => $set('slug', Str::slug($state))
                                                ),
                                            TextInput::make('slug')
                                                ->disabled()
                                                ->dehydrated()
                                                ->maxLength(255)
                                                ->unique(Category::class, 'slug', ignoreRecord: true),
                                            Textarea::make('description')
                                                ->maxLength(1000)
                                                ->placeholder('Category description'),
                                        ])
                                        ->createOptionAction(
                                            fn (Action $action) => $action
                                                ->modalHeading('Create new category')
                                                ->modalSubmitActionLabel('Create category')
                                        ),

                                    Select::make('parent_id')
                                        ->label('Parent Product')
                                        ->relationship('parent', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Select a parent product (optional)')
                                        ->helperText('Jika produk ini adalah varian (child), pilih produk utamanya (parent).'),

                                    TextInput::make('pax')
                                        ->label('Capacity (pax)')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1000)
                                        ->suffix('people')
                                        ->placeholder('1000'),

                                    TextInput::make('price')
                                        ->prefix('Rp')
                                        ->readOnly()
                                        ->label('Product Price')
                                        ->reactive()
                                        ->minValue(0)
                                        ->live()
                                        ->dehydrated()
                                        ->formatStateUsing(fn ($state) => number_format(is_numeric($state) ? $state : self::stripCurrency($state), 0, '.', ','))
                                        ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                                        ->helperText('Total Harga Publish - Total Pengurangan + Total Penambahan')
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record) {
                                                $final = (int) ($record->product_price ?? 0)
                                                    - (int) ($record->pengurangan ?? 0)
                                                    + (int) ($record->penambahan_publish ?? 0);
                                                $component->state($final);
                                            }
                                        }),

                                    TextInput::make('stock')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(10)
                                        ->suffix('units')
                                        ->placeholder('0')
                                        ->helperText('pastikan di isi dengan angka 10'),

                                ]),

                                Section::make('Product Status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Product Status')
                                            ->helperText('Aktifkan untuk menampilkan/menyembunyikan produk')
                                            ->default(true)
                                            ->onIcon('heroicon-s-check-circle')
                                            ->offIcon('heroicon-s-x-circle')
                                            ->onColor('success')
                                            ->offColor('danger'),
                                        Toggle::make('is_approved')
                                            ->label('Approval Status')
                                            ->helperText('Setujui atau tolak produk')
                                            ->default(false)
                                            ->onIcon('heroicon-s-hand-thumb-up')
                                            ->offIcon('heroicon-s-hand-thumb-down')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->visible(fn () => Auth::user()->hasRole('super_admin')),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Basic Facilities')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('product_price')
                                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                                            ->prefix('Rp')
                                            ->label('Total Publish Price')
                                            ->readOnly()
                                            ->live()
                                            ->minValue(0)
                                            ->dehydrated(true)
                                            ->helperText('Dihitung otomatis dari harga vendor')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $total = $record->items->sum(function ($item) {
                                                        $qty = (int) ($item->quantity ?? 1);
                                                        $publish = (int) ($item->harga_publish ?? 0);
                                                        $pricePublic = (int) ($item->price_public ?? ($publish * $qty));
                                                        return $pricePublic;
                                                    });
                                                    $component->state($total);
                                                }
                                            }),
                                        TextInput::make('vendorTotal')
                                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                                            ->prefix('Rp')
                                            ->label('Total Vendor Price')
                                            ->readOnly()
                                            ->minValue(0)
                                            ->helperText('Jumlah dari semua harga vendor')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $total = $record->items->sum('total_price');
                                                    $component->state((int) $total);
                                                }
                                            }),
                                    ]),
                                self::getVendorRepeater(),
                            ]),
                        Tab::make('Pengurangan Harga')
                            ->icon('heroicon-o-receipt-refund') // Changed icon
                            ->label('Pengurangan Harga (Jika Ada)') // Corrected typo
                            ->schema([
                                TextInput::make('pengurangan')
                                        ->label('Total Pengurangan')
                                        ->readOnly() 
                                        ->default(0)
                                        ->live()
                                        ->dehydrated()
                                        ->formatStateUsing(fn ($state) => number_format(is_numeric($state) ? $state : self::stripCurrency($state), 0, '.', ','))
                                        ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                                    ->prefix('Rp')
                                    ->helperText('Dihitung otomatis dari harga diskon')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $total = $record->pengurangans->sum('amount');
                                            $component->state($total);
                                        }
                                    }),
                                self::getDiscountRepeater(),
                            ]),
                        Tab::make('Penambahan Harga')
                            ->icon('heroicon-o-plus-circle')
                            ->label('Penambahan Harga (Jika Ada)')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('penambahan_publish')
                                            ->label('Total Publish Price')
                                            ->readOnly() 
                                            ->default(0)
                                            ->live()
                                            ->dehydrated()
                                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                                            ->prefix('Rp')
                                            ->helperText('Dihitung otomatis dari penambahan harga publish')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $total = $record->penambahanHarga->sum('harga_publish');
                                                    $component->state($total);
                                                }
                                            }),
                                        TextInput::make('penambahan_vendor')
                                            ->label('Total Vendor Price')
                                            ->readOnly() 
                                            ->default(0)
                                            ->live()
                                            ->dehydrated()
                                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                                            ->prefix('Rp')
                                            ->helperText('Dihitung otomatis dari penambahan harga vendor')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record) {
                                                    $total = $record->penambahanHarga->sum('harga_vendor');
                                                    $component->state($total);
                                                }
                                            }),
                                    ]),
                                self::getAdditionRepeater(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                static::getEloquentQuery()->with([
                    'items.vendor:id,name,harga_publish,harga_vendor,description',
                    'penambahanHarga.vendor:id,name,harga_publish,harga_vendor,description',
                ])
            )
            ->poll('5s')
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->tooltip(fn (Product $record): string => $record->price)
                    ->copyable()
                    ->copyMessage('Product name copied')
                    ->copyMessageDuration(1500)
                    ->description(function (Product $record): string {
                        $priceValue = $record->price;
                        if ($priceValue === null || ! is_numeric($priceValue)) {
                            return 'Rp -';
                        }

                        return 'Rp '.number_format((int) $priceValue, 0, '.', ',');
                    }),

                // TextColumn::make('id')
                //     ->label('SKU/ID'),

                TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Vendors')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 3 => 'success',
                        $state > 1 => 'info',
                        default => 'warning',
                    }
                    )
                    ->tooltip('Number of vendors associated with this product'),

                TextColumn::make('unique_orders_count')
                    ->label('In Orders')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->tooltip('Number of unique orders this product is part of.'),

                TextColumn::make('total_quantity_sold')
                    ->label('Total Sold')
                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, '.', ','))
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Total quantity of this product sold across all orders.'),

                TextColumn::make('price')
                    ->label('Total Price')
                    ->getStateUsing(function (Product $record) {
                        return $record->product_price 
                             - $record->pengurangans->sum('amount') 
                             + $record->penambahanHarga->sum('harga_publish');
                    })
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                    ->sortable()
                    ->alignEnd()
                    ->badge(),

                TextColumn::make('product_price')
                    ->label('Harga Paket')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                    ->sortable()
                    ->alignEnd()
                    ->badge(),

                TextColumn::make('pengurangan')
                    ->label('Pengurangan')
                    ->getStateUsing(fn ($record) => $record->pengurangans->sum('amount'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                    ->alignEnd()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'warning' : 'danger'),

                TextColumn::make('penambahan')
                    ->label('Penambahan Publish')
                    ->getStateUsing(fn ($record) => $record->penambahanHarga->sum('harga_publish'))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                    ->alignEnd()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'warning' : 'success'),

                TextColumn::make('pax')
                    ->label('Capacity')
                    ->suffix(' pax')
                    ->alignCenter()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 0,
                        thousandsSeparator: '.',
                    )
                    ->color(fn (int $state): string => match (true) {
                        $state > 1000 => 'success',
                        $state > 500 => 'info',
                        default => 'gray',
                    }
                    ),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status')
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->tooltip(fn (bool $state): string => $state ? 'Product is active' : 'Product is inactive'
                    ),

                IconColumn::make('is_approved')
                    ->boolean()
                    ->label('Approved')
                    ->alignCenter()
                    ->trueIcon('heroicon-s-hand-thumb-up')
                    ->falseIcon('heroicon-s-hand-thumb-down')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->tooltip(fn (bool $state): string => $state ? 'Product is approved' : 'Product is not approved'
                    ),
                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn (Product $record): string => 'Created: '.$record->created_at->diffForHumans()
                    ),

                TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

                SelectFilter::make('is_approved')
                    ->label('Approved')
                    ->options([
                        1 => 'Approved',
                        0 => 'Not Approved',
                    ]),

                Filter::make('vendor_usage')
                    ->label('Vendor Usage')
                    ->schema([
                        Select::make('usage')
                            ->label('Filter')
                            ->options([
                                'with' => 'Dengan Vendor',
                                'without' => 'Tanpa Vendor',
                            ])
                            ->placeholder('Semua Produk'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            ($data['usage'] ?? null) === 'with',
                            fn (Builder $q): Builder => $q->whereHas('items'),
                        )->when(
                            ($data['usage'] ?? null) === 'without',
                            fn (Builder $q): Builder => $q->whereDoesntHave('items'),
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! empty($data['usage'])) {
                            return 'Vendor: '.($data['usage'] === 'with' ? 'Ada' : 'Tidak Ada');
                        }

                        return null;
                    }),

                Filter::make('price_range')
                    ->label('Rentang Harga')
                    ->schema([
                        TextInput::make('min')
                            ->numeric()
                            ->placeholder('Min'),
                        TextInput::make('max')
                            ->numeric()
                            ->placeholder('Max'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $min = $data['min'] ?? null;
                        $max = $data['max'] ?? null;

                        return $query
                            ->when($min !== null && $min !== '', fn (Builder $q): Builder => $q->where('price', '>=', $min))
                            ->when($max !== null && $max !== '', fn (Builder $q): Builder => $q->where('price', '<=', $max));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $min = $data['min'] ?? null;
                        $max = $data['max'] ?? null;

                        if ($min !== null || $max !== null) {
                            if ($min && $max) {
                                return 'Harga: Rp '.$min.' - Rp '.$max;
                            }
                            if ($min) {
                                return 'Harga >= Rp '.$min;
                            }
                            if ($max) {
                                return 'Harga <= Rp '.$max;
                            }
                        }

                        return null;
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),

                    // Aksi Preview Detail
                    Action::make('preview_details')
                        ->label('Preview Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info') // Warna tombol/link
                        ->url(fn (Product $record): string => route('products.details', ['product' => $record, 'action' => 'preview'])) // <-- Use 'products.details'
                        ->openUrlInNewTab() // Buka di tab baru
                        ->tooltip('Lihat detail lengkap produk di tab baru'),
                    DeleteAction::make(),
                    Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalDescription('Do you want to duplicate this product and its vendor relations?')
                        ->modalSubmitActionLabel('Yes, duplicate product')
                        ->action(function (Product $record) {
                            // Duplicate main product
                            $attributes = $record->only([
                                'category_id',
                                'price',
                                'is_active',
                                'pax',
                            ]);

                            $duplicate = new Product($attributes);
                            $duplicate->name = "{$record->name} (Copy)";
                            $duplicate->slug = Product::generateUniqueSlug($duplicate->name);
                            $duplicate->save();

                            // Duplicate vendor relationships with all fields
                            foreach ($record->items as $item) {
                                $duplicate->items()->create([
                                    'vendor_id' => $item->vendor_id,
                                    'harga_publish' => $item->harga_publish,
                                    'quantity' => $item->quantity,
                                    'price_public' => $item->price_public,
                                    'total_price' => $item->total_price,
                                    'harga_vendor' => $item->harga_vendor,
                                    'description' => $item->description,
                                ]);
                            }

                            Notification::make()
                                ->success()
                                ->title('Product duplicated successfully')
                                ->send();
                        })
                        ->tooltip('Duplicate this product'),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Product $record) {
                            $record->update(['is_approved' => true]);
                            Notification::make()->title('Product Approved')->success()->send();
                        })
                        ->visible(fn (Product $record): bool => ! $record->is_approved && Auth::user()->hasRole('super_admin'))
                        ->tooltip('Approve this product'),

                    Action::make('disapprove')
                        ->label('Disapprove')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Product $record) {
                            $record->update(['is_approved' => false]);
                            Notification::make()->title('Product Disapproved')->warning()->send();
                        })
                        ->visible(fn (Product $record): bool => $record->is_approved && Auth::user()?->hasRole('super_admin'))
                        ->tooltip('Disapprove this product'),

                ])
                    ->tooltip('Available actions')
                    ->tooltip('Available actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Ganti ExportBulkAction bawaan Filament
                    BulkAction::make('export_selected_maatwebsite')
                        ->label('Export Selected (Excel)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            return Excel::download(new ProductExport($records->pluck('id')->toArray()), 'products_export_'.now()->format('YmdHis').'.xlsx');
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation(),
                    RestoreBulkAction::make(),
                    BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => true]);
                            Notification::make()
                                ->title('Products Activated')
                                ->body(count($records).' product(s) have been activated.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_active' => false]);
                            Notification::make()
                                ->title('Products Deactivated')
                                ->body(count($records).' product(s) have been deactivated.')
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-s-hand-thumb-up')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->update(['is_approved' => true]);
                            Notification::make()
                                ->title('Products Approved')
                                ->body(count($records).' product(s) have been approved.')
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => Auth::user()->hasRole('super_admin'))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->defaultPaginationPageOption(10)
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create product')
                    ->url(route('filament.admin.resources.products.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->paginationPageOptions([10, 25, 50]);
    }

    protected static function getVendorRepeater()
    {
        return Repeater::make('items')
            ->label('Vendors')
            ->relationship()
            ->schema([
                Grid::make(4)
                    ->schema([
                        Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a vendor')
                            ->required()
                            ->live()
                            ->reactive()
                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    self::updateVendorData($set, $state);
                                    self::calculatePrices($get, $set);
                                }
                            })
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    self::updateVendorData($set, $state);
                                    self::calculatePrices($get, $set);
                                }
                            })
                            ->columnSpan([
                                'md' => 5,
                            ]),

                        TextInput::make('harga_publish')
                            ->label('Published Price')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::calculatePrices($get, $set);
                            }),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::calculatePrices($get, $set);
                            }),

                        TextInput::make('price_public')
                            ->label('Public Price')
                            ->prefix('Rp')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                            ->reactive()
                            ->dehydrated()
                            ->helperText('Harga publish × kuantitas'),

                        TextInput::make('harga_vendor')
                            ->label('Vendor Price')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format(self::stripCurrency($state), 0, '.', ','))
                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                            ->readOnly()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::calculatePrices($get, $set);
                            }),

                        Hidden::make('total_price')
                            ->dehydrated(),

                        RichEditor::make('description')
                            ->label('Additional Notes')
                            ->columnSpanFull(),
                    ]),
            ])
            ->extraItemActions([
                Action::make('openVendor')
                    ->label('Open Vendor')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('info')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);
                        $vendorId = $itemData['vendor_id'] ?? null;
                        if (! $vendorId) {
                            return null;
                        }
                        $vendor = static::getVendorData($vendorId);

                        return $vendor ? VendorResource::getUrl('edit', ['record' => $vendor]) : null;
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['vendor_id'] ?? null)),
            ])
            ->defaultItems(1)
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['vendor_id']
                    ? static::getVendorData($state['vendor_id'])?->name ?? 'Unnamed Vendor'
                    : 'New Facility'
            )
            ->reorderable()
            ->cloneable()
            ->reactive()
            ->live()
            ->afterStateUpdated(function (Get $get, Set $set) {
                // $get is relative to the repeater's parent (the Tab)
                // $state (third argument, if defined) would be the array of items.
                // Let's get items directly using $get('items') if $state is not used.
                $itemsArray = $get('items') ?? []; // 'items' is the repeater name, $get('items') gets its state.

                // Calculate total product price from vendor items
                $totalProductPrice = collect($itemsArray)
                    ->sum(function ($item) {
                        $val = $item['price_public'] ?? 0;
                        return self::stripCurrency($val);
                    });

                $set('product_price', self::formatCurrency($totalProductPrice)); // Sets 'product_price' field in the same Tab

                // Calculate total vendor price from vendor items' harga_vendor
                $totalVendorPrice = collect($itemsArray)
                    ->sum(function ($item) {
                        $val = $item['harga_vendor'] ?? 0;
                        return self::stripCurrency($val);
                    });

                $set('vendorTotal', self::formatCurrency($totalVendorPrice)); // Sets 'vendorTotal' field in the same Tab

                // Now, update the final product price.
                $penguranganVal = self::getCleanInt($get, '../pengurangan'); // Get 'pengurangan' from other Tab
                $finalPrice = $totalProductPrice - $penguranganVal;
                $set('../price', self::formatCurrency($finalPrice)); // Set 'price' in the "Basic Information" Tab
            })
            ->columns(1);
    }

    /**
     * Cache for vendor data to avoid repeated database queries
     */
    protected static array $vendorCache = [];

    /**
     * Get vendor data with caching to optimize performance
     */
    protected static function getVendorData($vendorId): ?object
    {
        if (! isset(static::$vendorCache[$vendorId])) {
            static::$vendorCache[$vendorId] = Vendor::find($vendorId);
        }

        return static::$vendorCache[$vendorId];
    }

    protected static function updateVendorData(Set $set, $vendorId): void
    {
        $vendor = static::getVendorData($vendorId);
        if ($vendor) {
            $active = $vendor->activePrice();
            $h_publish = $active?->harga_publish ?? $vendor->harga_publish;
            $h_vendor = $active?->harga_vendor ?? $vendor->harga_vendor;
            
            $set('harga_publish', self::formatCurrency((int) $h_publish));
            $set('harga_vendor', self::formatCurrency((int) $h_vendor));
            $set('description', $vendor->description);
        }
    }

    protected static function updateAdditionVendorData(Set $set, $vendorId): void
    {
        $vendor = static::getVendorData($vendorId);
        if ($vendor) {
            $active = $vendor->activePrice();
            $h_publish = $active?->harga_publish ?? $vendor->harga_publish;
            $h_vendor = $active?->harga_vendor ?? $vendor->harga_vendor;
            
            $set('harga_publish', self::formatCurrency((int) $h_publish));
            $set('harga_vendor', self::formatCurrency((int) $h_vendor));
            $set('description', $vendor->description);
        }
    }

    protected static function calculateAdditionPrices(Get $get, Set $set): void
    {
        // Get base values for addition items
        $harga_publish = self::getCleanInt($get, 'harga_publish');
        $harga_vendor = self::getCleanInt($get, 'harga_vendor');

        // Update the total addition prices
        self::calculateTotalAdditionPrice($get, $set);
    }

    // Helper untuk mengambil nilai integer dari state yang mungkin berupa string berformat
    protected static function getCleanInt(Get $get, string $path): int
    {
        return self::stripCurrency($get($path));
    }

    // Helper untuk membersihkan format currency (koma dan titik) menjadi integer
    protected static function stripCurrency($val): int
    {
        if (is_string($val)) {
            $val = str_replace(['.', ','], '', $val);
        }
        return (int) ($val ?? 0);
    }

    // Helper untuk memformat integer ke string ribuan
    protected static function formatCurrency(int $value): string
    {
        return number_format($value, 0, '.', ',');
    }

    protected static function calculatePrices(Get $get, Set $set): void
    {
        // Get base values
        $harga_publish = self::getCleanInt($get, 'harga_publish');
        $harga_vendor = self::getCleanInt($get, 'harga_vendor');
        $quantity = (int) ($get('quantity') ?? 1);
        
        // Calculate price_public (harga_publish * quantity)
        $price_public = $harga_publish * $quantity;
        $set('price_public', self::formatCurrency($price_public));

        // Calculate total_price (harga_vendor * quantity)
        $total_price = $harga_vendor * $quantity;
        $set('total_price', $total_price);

        // Update the total product price
        self::calculateTotalProductPrice($get, $set);
    }

    /**
     * Calculate the total product_price from all vendor items and update the final product price.
     * Triggered by changes in the vendor repeater.
     */
    protected static function calculateTotalProductPrice(Get $get, Set $set): void
    {
        // Get all items from the repeater
        $items = $get('../../items') ?? [];

        // Calculate total price from all items' price_public values
        $total_price = collect($items)
            ->sum(function ($item) {
                $val = $item['price_public'] ?? 0;
                return self::stripCurrency($val);
            });

        // Set the overall product price
        $set('../../product_price', self::formatCurrency($total_price));

        // Calculate total vendor price from all items' harga_vendor values
        $total_vendor_price = collect($items)
            ->sum(function ($item) {
                $val = $item['harga_vendor'] ?? 0;
                return self::stripCurrency($val);
            });
        
        $set('../../vendorTotal', self::formatCurrency($total_vendor_price));

        self::updateFinalProductPrice($get, $set);
    }

    /**
     * Calculate the total addition prices from all addition items.
     * Triggered by changes in the addition repeater.
     */
    protected static function calculateTotalAdditionPrice(Get $get, Set $set): void
    {
        // Get all addition items from the repeater
        $additionItems = $get('../../penambahanHarga') ?? [];

        // Calculate total publish price from all addition items' harga_publish values
        $total_publish_price = collect($additionItems)
            ->sum(function ($item) {
                $val = $item['harga_publish'] ?? 0;
                return self::stripCurrency($val);
            });

        // Calculate total vendor price from all addition items' harga_vendor values
        $total_vendor_price = collect($additionItems)
            ->sum(function ($item) {
                $val = $item['harga_vendor'] ?? 0;
                return self::stripCurrency($val);
            });

        // Set the addition totals
        $set('../../penambahan_publish', self::formatCurrency($total_publish_price));
        $set('../../penambahan_vendor', self::formatCurrency($total_vendor_price));

        self::updateFinalProductPriceWithAdditions($get, $set);
    }

    /**
     * Updates the final sell price (Product.price) based on Product.product_price, Product.pengurangan, and Product.penambahan_publish.
     * Called when Product.product_price (from vendors), Product.pengurangan (from discounts), or Product.penambahan_publish (from additions) changes.
     */
    protected static function updateFinalProductPrice(Get $get, Set $set): void
    {
        // Paths are relative to the repeater item context that initiated the chain of updates.
        $productPriceFromVendors = self::getCleanInt($get, '../../product_price');
        $totalPenguranganFromDiscounts = self::getCleanInt($get, '../../pengurangan');
        $totalPenambahanFromAdditions = self::getCleanInt($get, '../../penambahan_publish');

        $finalPrice = $productPriceFromVendors - $totalPenguranganFromDiscounts + $totalPenambahanFromAdditions;
        $set('../../price', self::formatCurrency($finalPrice)); // Sets Product.price (in Basic Information tab)
    }

    /**
     * Updates the final sell price specifically from addition changes.
     */
    protected static function updateFinalProductPriceWithAdditions(Get $get, Set $set): void
    {
        // Paths are relative to the repeater item context that initiated the chain of updates.
        $productPriceFromVendors = self::getCleanInt($get, '../../product_price');
        $totalPenguranganFromDiscounts = self::getCleanInt($get, '../../pengurangan');
        $totalPenambahanFromAdditions = self::getCleanInt($get, '../../penambahan_publish');

        $finalPrice = $productPriceFromVendors - $totalPenguranganFromDiscounts + $totalPenambahanFromAdditions;
        $set('../../price', self::formatCurrency($finalPrice)); // Sets Product.price (in Basic Information tab)
    }

    protected static function getDiscountRepeater()
    {
        return Repeater::make('itemsPengurangan')
            ->relationship()
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextInput::make('description')
                            ->label('Nama Vendor')
                            ->required()
                            ->columnSpan(3), // Full span for description

                        TextInput::make('amount')
                            ->label('Discount Value')
                            ->required()
                            ->prefix('Rp')      // Always 'Rp' as it's a fixed amount
                            ->rules(['min:0'])     // Simple min:0 rule
                            ->formatStateUsing(fn ($state) => number_format(is_numeric($state) ? $state : self::stripCurrency($state), 0, '.', ','))
                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                            ->columnSpan(3), // Adjusted column span

                        RichEditor::make('notes')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ]),
            ])
            ->defaultItems(0)
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['description'] ?? 'New Discount Item'
            )
            ->reorderable()
            ->cloneable()
            // ->reactive()
            // ->live()
            ->afterStateUpdated(function (Get $get, Set $set, $state) { // $state is the array of itemsPengurangan
                // $get is relative to the repeater's parent (the "Pengurangan Harga" Tab)
                $totalPengurangan = collect($state)
                    ->sum(function ($item) {
                        $amountStr = $item['amount'] ?? '0';
                        return self::stripCurrency($amountStr);
                    });

                // Set the 'pengurangan' field in the current Tab ("Pengurangan Harga")
                $set('pengurangan', self::formatCurrency($totalPengurangan));

                // Now, calculate and set the final 'price' field (in "Basic Information" Tab)
                $productPriceVal = self::getCleanInt($get, '../product_price'); // Get 'product_price' from other Tab
                $penambahanVal = self::getCleanInt($get, '../penambahan_publish'); // Get 'penambahan_publish' from other Tab
                $finalPrice = $productPriceVal - $totalPengurangan + $penambahanVal;
                $set('../price', self::formatCurrency($finalPrice)); // Set 'price' in other Tab
            })
            ->addActionLabel('Add Discount')
            ->columns(1);
    }

    protected static function getAdditionRepeater()
    {
        return Repeater::make('penambahanHarga')
            ->relationship()
            ->schema([
                Grid::make(4)
                    ->schema([
                        Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a vendor')
                            ->required()
                            ->live()
                            ->reactive()
                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    self::updateAdditionVendorData($set, $state);
                                    self::calculateAdditionPrices($get, $set);
                                }
                            })
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    self::updateAdditionVendorData($set, $state);
                                    self::calculateAdditionPrices($get, $set);
                                }
                            })
                            ->columnSpan([
                                'md' => 2,
                            ]),

                        TextInput::make('harga_publish')
                            ->label('Published Price')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format(is_numeric($state) ? $state : self::stripCurrency($state), 0, '.', ','))
                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::calculateAdditionPrices($get, $set);
                            }),

                        TextInput::make('harga_vendor')
                            ->label('Vendor Price')
                            ->prefix('Rp')
                            ->formatStateUsing(fn ($state) => number_format(is_numeric($state) ? $state : self::stripCurrency($state), 0, '.', ','))
                            ->dehydrateStateUsing(fn ($state) => self::stripCurrency($state))
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::calculateAdditionPrices($get, $set);
                            }),

                        RichEditor::make('description')
                            ->label('Description/Notes')
                            ->placeholder('Additional notes for this item')
                            ->columnSpanFull(),
                    ]),
            ])
            ->extraItemActions([
                Action::make('openVendor')
                    ->label('Open Vendor')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('info')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);
                        $vendorId = $itemData['vendor_id'] ?? null;
                        if (! $vendorId) {
                            return null;
                        }
                        $vendor = static::getVendorData($vendorId);

                        return $vendor ? VendorResource::getUrl('edit', ['record' => $vendor]) : null;
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['vendor_id'] ?? null)),
            ])
            ->defaultItems(0)
            ->collapsed()
            ->itemLabel(fn (array $state): ?string => $state['vendor_id']
                    ? static::getVendorData($state['vendor_id'])?->name ?? 'Unnamed Vendor'
                    : 'New Addition Item'
            )
            ->reorderable()
            ->cloneable()
            ->afterStateUpdated(function (Get $get, Set $set, $state) { // $state is the array of penambahanHarga
                // $get is relative to the repeater's parent (the "Penambahan Harga" Tab)
                $totalPenambahanPublish = collect($state)
                    ->sum(function ($item) {
                        $val = $item['harga_publish'] ?? 0;
                        return self::stripCurrency($val);
                    });

                $totalPenambahanVendor = collect($state)
                    ->sum(function ($item) {
                        $val = $item['harga_vendor'] ?? 0;
                        return self::stripCurrency($val);
                    });

                // Set the 'penambahan_publish' and 'penambahan_vendor' fields in the current Tab ("Penambahan Harga")
                $set('penambahan_publish', self::formatCurrency($totalPenambahanPublish));
                $set('penambahan_vendor', self::formatCurrency($totalPenambahanVendor));

                // Now, calculate and set the final 'price' field (in "Basic Information" Tab)
                $productPriceVal = self::getCleanInt($get, '../product_price'); // Get 'product_price' from other Tab
                $penguranganVal = self::getCleanInt($get, '../pengurangan'); // Get 'pengurangan' from other Tab
                $finalPrice = $productPriceVal - $penguranganVal + $totalPenambahanPublish;
                $set('../price', self::formatCurrency($finalPrice)); // Set 'price' in other Tab
            })
            ->addActionLabel('Add Additional Item')
            ->columns(1);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Data Produk yang telah dibuat dan dikelola';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
            'view' => ViewProduct::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount([
                'orders as unique_orders_count',
            ])
            // Bonus: Ini juga akan mengaktifkan kolom 'Total Sold'
            ->withSum('orderItems as total_quantity_sold', 'quantity');
    }

    /**
     * Clear vendor cache to free memory
     */
    protected static function clearVendorCache(): void
    {
        static::$vendorCache = [];
    }

    // Ensure these lifecycle hooks call the server-side recalculation method
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $result = static::mutateFormDataBeforeSave($data);
        static::clearVendorCache(); // Clear cache after processing

        return $result;
    }

    protected function mutateFormDataBeforeUpdate(array $data): array
    {
        // Preserve existing image if not changed
        // This logic might need adjustment based on how FileUpload handles empty states
        // For now, we assume $data will not contain 'image' if it's not being updated.
        $result = static::mutateFormDataBeforeSave($data);
        static::clearVendorCache(); // Clear cache after processing

        return $result;
    }

    /**
     * Mutate form data before saving (both create and update).
     * This method recalculates product_price, pengurangan, penambahan, and price on the server-side
     * based on the submitted repeater data to ensure data integrity.
     */
    protected static function mutateFormDataBeforeSave(array $data): array
    {
        // Helper function to clean currency string values and convert to float
        $cleanCurrencyValue = function ($value): int {
            return self::stripCurrency($value);
        };

        // 1. Recalculate 'product_price' from 'items' (vendor repeater)
        $calculatedProductPrice = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                // 'price_public' is 'harga_publish' * 'quantity' for each vendor item
                $calculatedProductPrice += $cleanCurrencyValue($item['price_public'] ?? '0');
            }
        }
        $data['product_price'] = $calculatedProductPrice;

        // 2. Recalculate 'pengurangan' from 'itemsPengurangan' (discount repeater)
        $calculatedPengurangan = 0;
        if (isset($data['itemsPengurangan']) && is_array($data['itemsPengurangan'])) {
            foreach ($data['itemsPengurangan'] as $item) {
                $calculatedPengurangan += $cleanCurrencyValue($item['amount'] ?? '0');
            }
        }
        $data['pengurangan'] = $calculatedPengurangan;

        // 3. Recalculate 'penambahan_publish' and 'penambahan_vendor' from 'penambahanHarga' (addition repeater)
        $calculatedPenambahanPublish = 0;
        $calculatedPenambahanVendor = 0;
        if (isset($data['penambahanHarga']) && is_array($data['penambahanHarga'])) {
            foreach ($data['penambahanHarga'] as $key => $item) {
                $calculatedPenambahanPublish += $cleanCurrencyValue($item['harga_publish'] ?? '0');
                $calculatedPenambahanVendor += $cleanCurrencyValue($item['harga_vendor'] ?? '0');

                // Set amount field to harga_publish for compatibility with database
                $data['penambahanHarga'][$key]['amount'] = $cleanCurrencyValue($item['harga_publish'] ?? '0');
            }
        }
        $data['penambahan_publish'] = $calculatedPenambahanPublish;
        $data['penambahan_vendor'] = $calculatedPenambahanVendor;

        // 4. Recalculate final 'price'
        $data['price'] = $data['product_price'] - $data['pengurangan'] + $data['penambahan_publish'];

        return $data;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Details')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Product Name')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->placeholder('-'),
                                                TextEntry::make('pax')
                                                    ->label('Capacity (pax)')
                                                    ->suffix(' people')
                                                    ->placeholder('-'),
                                                TextEntry::make('stock')
                                                    ->weight('bold')
                                                    ->suffix(' units')
                                                    ->color(fn (string $state): string => $state > 0 ? 'primary' : 'danger'),
                                            ]),
                                    ]),
                                Section::make('Facilities')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('product_price')
                                                    ->label('Total Publish Price')
                                                    ->weight('bold')
                                                    ->color('primary')
                                                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                                    ->helperText('Total Harga Publish Vendor')
                                                    ->placeholder('-'),
                                                TextEntry::make('pengurangan')
                                                    ->label('Pengurangan Harga')
                                                    ->weight('bold')
                                                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                                    ->helperText('Total Pengurangan')
                                                    ->color('danger')
                                                    ->placeholder('-'),
                                            ]),
                                    ]),
                                Section::make('Product Status')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                IconEntry::make('is_active')
                                                    ->label('Product Status')
                                                    ->boolean(),
                                                IconEntry::make('is_approved')
                                                    ->label('Approval Status')
                                                    ->boolean()
                                                    ->visible(fn () => Auth::user()->hasRole('super_admin')),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Basic Facilities')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('product_price')
                                            ->label('Total Publish Price')
                                            ->weight('bold')
                                            ->color('primary') // Warna untuk total harga vendor
                                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                            ->helperText('Sum of all vendor prices'),

                                        TextEntry::make('calculatedPriceVendor')
                                            ->label('Total Vendor Cost')
                                            ->weight('bold')
                                            ->color('warning')
                                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                            ->helperText('Sum of all vendor prices')
                                            ->state(function (Product $record): int {
                                                return $record->items->sum(function ($item) {
                                                    // Access the accessor: $item->harga_vendor * $item->quantity
                                                    return $item->harga_vendor;
                                                });
                                            }),
                                    ]),
                                Section::make()
                                    ->schema([
                                        RepeatableEntry::make('items')
                                            ->schema([
                                                TextEntry::make('vendor.name')
                                                    ->label('Vendor Name')
                                                    ->placeholder('-'),
                                                TextEntry::make('harga_publish')
                                                    ->label('Published Price')
                                                    ->weight('bold')
                                                    ->color('info')
                                                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                                    ->placeholder('-'),
                                                TextEntry::make('quantity')
                                                    ->placeholder('-')
                                                    ->color('gray'),
                                                TextEntry::make('price_public')
                                                    ->label('Calculated Public Price')
                                                    ->weight('bold')
                                                    ->color('primary')
                                                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                                    ->placeholder('-'),
                                                TextEntry::make('harga_vendor')
                                                    ->label('Vendor Unit Cost')
                                                    ->weight('bold')
                                                    ->color('warning')
                                                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                                    ->placeholder('-'),
                                                TextEntry::make('calculate_price_vendor')
                                                    ->label('Calculated Vendor Cost')
                                                    ->weight('bold')
                                                    ->color('warning')
                                                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                                    ->placeholder('-'), // Will use ProductVendor's accessor
                                                TextEntry::make('description')
                                                    ->label('Fasilitas')
                                                    ->columnSpanFull()
                                                    ->html()
                                                    ->placeholder('Keterangan Fasilitas'),
                                            ])
                                            ->columns(4) // Adjusted columns due to new entry
                                            ->grid(1)
                                            ->contained(true),
                                    ]),
                            ]),

                        Tab::make('Pengurangan Harga')
                            ->icon('heroicon-o-receipt-refund')
                            ->label('Pengurangan Harga (Jika Ada)')
                            ->schema([
                                TextEntry::make('pengurangan')
                                    ->label('Total Pengurangan')
                                    ->color('danger') // Warna untuk total pengurangan
                                    ->weight('bold')
                                    ->placeholder('-')
                                    ->state(function (Product $record): int {
                                        // Jika 'pengurangan' adalah kolom di tabel Product
                                        // return $record->pengurangan ?? 0;
                                        // Jika 'pengurangan' dihitung dari relasi itemsPengurangan
                                        return $record->itemsPengurangan()->sum('amount');
                                    })
                                    ->helperText('Sum of all discount items'),
                                RepeatableEntry::make('itemsPengurangan')
                                    ->label('Discount Items')
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                        TextEntry::make('amount')
                                            ->label('Discount Value')
                                            ->color('warning') // Warna untuk nilai diskon
                                            ->weight('bold')
                                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                            ->placeholder('-'),
                                        TextEntry::make('notes')
                                            ->label('Notes')
                                            ->columnSpanFull()
                                            ->html()
                                            ->placeholder('No notes.'),
                                    ])
                                    ->columns(2)
                                    ->grid(1)
                                    ->contained(true),
                            ]),

                        Tab::make('Penambahan Harga')
                            ->icon('heroicon-o-plus-circle')
                            ->label('Penambahan Harga (Jika Ada)')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('penambahan_publish')
                                            ->label('Total Penambahan Publish Price')
                                            ->color('success') // Warna untuk total penambahan
                                            ->weight('bold')
                                            ->placeholder('-')
                                            ->state(function (Product $record): int {
                                                // Ambil dari kolom penambahan_publish atau hitung dari relasi harga_publish
                                                return $record->penambahan_publish ?? $record->penambahanHarga()->sum('harga_publish');
                                            })
                                            ->helperText('Sum of all additional publish prices'),
                                        TextEntry::make('penambahan_vendor')
                                            ->label('Total Penambahan Vendor Price')
                                            ->color('warning') // Warna untuk vendor price
                                            ->weight('bold')
                                            ->placeholder('-')
                                            ->state(function (Product $record): int {
                                                // Ambil dari kolom penambahan_vendor atau hitung dari relasi harga_vendor
                                                return $record->penambahan_vendor ?? $record->penambahanHarga()->sum('harga_vendor');
                                            })
                                            ->helperText('Sum of all additional vendor prices'),
                                    ]),
                                RepeatableEntry::make('penambahanHarga')
                                    ->label('Additional Items')
                                    ->schema([
                                        TextEntry::make('vendor.name')
                                            ->label('Vendor Name')
                                            ->placeholder('-')
                                            ->weight('bold')
                                            ->color('info'),
                                        TextEntry::make('harga_publish')
                                            ->label('Publish Price')
                                            ->color('success') // Warna untuk harga publish
                                            ->weight('bold')
                                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                            ->placeholder('-'),
                                        TextEntry::make('harga_vendor')
                                            ->label('Vendor Price')
                                            ->color('warning') // Warna untuk harga vendor
                                            ->weight('bold')
                                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, '.', ','))
                                            ->placeholder('-'),
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->columnSpanFull()
                                            ->html()
                                            ->placeholder('No description.'),
                                    ])
                                    ->columns(3)
                                    ->grid(1)
                                    ->contained(true),
                            ]),
                        Tab::make('Timestamps')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created On')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Last Modified')
                                    ->dateTime(),
                                TextEntry::make('user.name') // Jika ada relasi user
                                    ->label('Created by')
                                    ->placeholder('-')
                                    ->visible(fn (Product $record) => $record->user !== null),
                                TextEntry::make('lastEditedBy.name')
                                    ->label('Last Edited By')
                                    ->placeholder('-')
                                    ->state(function (Product $record): string {
                                        if ($record->lastEditedBy) {
                                            return $record->lastEditedBy->name;
                                        }

                                        // Fallback untuk data lama yang belum memiliki track editor
                                        if ($record->updated_at && $record->created_at && $record->updated_at->ne($record->created_at)) {
                                            return 'Modified on '.$record->updated_at->format('M d, Y H:i');
                                        }

                                        return 'No modifications yet';
                                    })
                                    ->helperText('Track who made the last changes to this product'),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
