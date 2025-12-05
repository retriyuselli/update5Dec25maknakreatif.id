<?php

namespace App\Filament\Resources\Vendors;

use App\Filament\Resources\Vendors\Pages\CreateVendor;
use App\Filament\Resources\Vendors\Pages\EditVendor;
use App\Filament\Resources\Vendors\Pages\ListVendors;
use App\Filament\Resources\Vendors\Pages\ViewVendor;
use App\Models\Category;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Vendor';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Manajemen Vendor')
                    ->tabs([
                        Tab::make('Informasi Dasar')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Section::make('Identitas Vendor')
                                    ->description('Informasi dasar tentang vendor')
                                    ->schema([
                                        Grid::make()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    // ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, Set $set, ?Vendor $record) {
                                                        // Add null check for $state
                                                        if ($state === null) {
                                                            $set('slug', '');

                                                            return;
                                                        }

                                                        $slug = Str::slug($state);

                                                        // Check if slug exists
                                                        $exists = Vendor::where('slug', $slug)
                                                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                                            ->exists();

                                                        // If exists, append timestamp
                                                        if ($exists) {
                                                            $slug = $slug.'-'.now()->timestamp;
                                                        }

                                                        $set('slug', $slug);
                                                    })
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->placeholder('nama vendor / nama pengantin_lokasi_pax'),

                                                TextInput::make('slug')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->unique(ignoreRecord: true),

                                                Select::make('category_id')
                                                    ->relationship('category', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->live(debounce: 500)
                                                            ->afterStateUpdated(fn ($state, Set $set) =>
                                                            // Add null check here too
                                                            $set('slug', $state ? Str::slug($state) : '')
                                                            ),
                                                        TextInput::make('slug')
                                                            ->disabled()
                                                            ->dehydrated()
                                                            ->unique(Category::class, 'slug', ignoreRecord: true),
                                                        Toggle::make('is_active')
                                                            ->required(),
                                                    ]),

                                                Select::make('status')
                                                    ->options([
                                                        'vendor' => 'Vendor',
                                                        'product' => 'Product',
                                                    ])
                                                    ->required(),

                                                TextInput::make('pic_name')
                                                    ->label('PIC Name')
                                                    ->required(),

                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->required()
                                                    ->prefix('+62')
                                                    // Ubah regex menjadi lebih fleksibel
                                                    // ->regex('/^[0-9]{8,15}$/') // Lebih fleksibel dari 9-15 menjadi 8-15 digit
                                                    ->placeholder('812XXXXXXXX')
                                                    ->helperText('Enter number without leading zero'),

                                                TextInput::make('address')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Section::make('Business Details')
                                    ->description('Additional business information')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                RichEditor::make('description')
                                                    ->columnSpanFull()
                                                    ->minLength(10)
                                                    ->required()
                                                    ->label('Description'),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Financial Information')
                            ->icon('heroicon-m-currency-dollar')
                            ->schema([
                                Section::make('Pricing')
                                    ->description('Manage vendor pricing and profit calculations')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('harga_publish')
                                                    ->label('Published Price')
                                                    ->required()
                                                    ->prefix('Rp')
                                                    ->stripCharacters(',')
                                                    ->live(onBlur: true)
                                                    ->default(0)
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        static::calculateProfitMetrics($set, $get);
                                                    }),

                                                TextInput::make('harga_vendor')
                                                    ->label('Vendor Price')
                                                    ->numeric()
                                                    ->required()
                                                    ->default(0)
                                                    ->prefix('Rp')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        static::calculateProfitMetrics($set, $get);
                                                    })
                                                    ->rules(['min:0']),

                                                TextInput::make('profit_margin')
                                                    ->label('Profit Margin')
                                                    ->numeric()
                                                    ->suffix('%')
                                                    ->disabled()
                                                    ->dehydrated(false),

                                                TextInput::make('profit_amount')
                                                    ->label('Profit Amount')
                                                    ->prefix('Rp. ')
                                                    // ->mask(RawJs::make('$money($input)'))
                                                    // ->stripCharacters(',')
                                                    ->numeric()
                                                    ->disabled()
                                                    ->dehydrated(false),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Section::make('Banking Information')
                                    ->description('Vendor banking details')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('bank_name')
                                                    ->label('Bank Name')
                                                    ->prefix('Bank '),

                                                TextInput::make('bank_account')
                                                    ->label('Account Number')
                                                    ->numeric(),

                                                TextInput::make('account_holder')
                                                    ->label('Account Holder Name')
                                                    ->helperText('Enter name exactly as it appears on the bank account')
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tab::make('Documents')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Section::make('Contract Documents')
                                    ->description('Upload and manage vendor contracts')
                                    ->schema([
                                        FileUpload::make('kontrak_kerjasama')
                                            ->label('Partnership Agreement')
                                            ->directory('vendor-contracts')
                                            ->preserveFilenames()
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(10240) // 10MB
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('Upload PDF file (max 10MB)')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                static::getEloquentQuery()->withCount([
                    'productVendors',
                    'expenses',
                    'notaDinasDetails',
                    'productPenambahans',
                ])
            )
            ->poll('5s')
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->copyMessage('Vendor copied')
                    ->description(fn (Vendor $record): string => $record->category?->name ?? '-'),

                // TextColumn::make('id')
                //     ->label('SKU/ID'),

                TextColumn::make('pic_name')
                    ->label('PIC')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(fn (string $state) => '+62 '.$state),

                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'vendor' => 'primary',
                        'product' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'vendor' => 'Vendor',
                        'product' => 'Product',
                        default => ucfirst($state),
                    }),

                TextColumn::make('harga_publish')
                    ->label('Published Price')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('end'),

                TextColumn::make('harga_vendor')
                    ->label('Vendor Price')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('end'),

                TextColumn::make('profit_amount')
                    ->label('Profit')
                    ->money('IDR')
                    ->state(function (Vendor $record): float {
                        return $record->harga_publish - $record->harga_vendor;
                    })
                    ->alignment('end')
                    ->color(fn (Vendor $record): string => ($record->harga_publish - $record->harga_vendor) > 0 ? 'success' : 'danger'
                    ),

                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bank_account')
                    ->label('Account Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('usage_status')
                    ->label('Usage Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'In Use' => 'warning',
                        'Available' => 'success',
                        default => 'gray',
                    })
                    ->tooltip(function (Vendor $record): string {
                        $details = $record->usage_details;
                        $descriptions = [];

                        if ($details['productCount'] > 0) {
                            $descriptions[] = "{$details['productCount']} product(s)";
                        }
                        if ($details['expenseCount'] > 0) {
                            $descriptions[] = "{$details['expenseCount']} expense(s)";
                        }
                        if ($details['notaDinasCount'] > 0) {
                            $descriptions[] = "{$details['notaDinasCount']} nota dinas detail(s)";
                        }
                        if ($details['productPenambahanCount'] > 0) {
                            $descriptions[] = "{$details['productPenambahanCount']} product addition(s)";
                        }

                        return ! empty($descriptions)
                            ? 'Used in: '.implode(', ', $descriptions)
                            : 'Not used in any products, expenses, nota dinas details, or product additions';
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated Date')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status')
                    ->options([
                        'vendor' => 'Vendor',
                        'product' => 'Product',
                    ])
                    ->multiple(),

                Filter::make('usage_status')
                    ->label('Usage Status')
                    ->schema([
                        Select::make('usage')
                            ->label('Filter by Usage')
                            ->options([
                                'in_use' => 'In Use',
                                'available' => 'Available',
                            ])
                            ->placeholder('All Vendors'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['usage'] === 'in_use',
                            fn (Builder $query): Builder => $query->whereHas('productVendors')
                                ->orWhereHas('expenses') // expenses
                                ->orWhereHas('notaDinasDetails') // nota dinas details
                                ->orWhereHas('productPenambahans'), // product additions
                        )->when(
                            $data['usage'] === 'available',
                            fn (Builder $query): Builder => $query->whereDoesntHave('productVendors')
                                ->whereDoesntHave('expenses') // expenses
                                ->whereDoesntHave('notaDinasDetails') // nota dinas details
                                ->whereDoesntHave('productPenambahans'), // product additions
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['usage']) {
                            return 'Usage: '.($data['usage'] === 'in_use' ? 'In Use' : 'Available');
                        }

                        return null;
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->icon('heroicon-m-trash')
                        ->tooltip('Delete vendor')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Vendor')
                        ->modalDescription('Are you sure you want to delete this vendor? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->visible(function (Vendor $record): bool {
                            return $record->usage_status === 'Available';
                        })
                        ->before(function (?Vendor $record) {
                            if (! $record) {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body('Vendor data not found. Please refresh the page and try again.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            Notification::make()
                                ->info()
                                ->title('Processing')
                                ->body('Validating vendor for deletion...')
                                ->send();
                        })
                        ->action(function (?Vendor $record) {
                            if (! $record) {
                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Failed')
                                    ->body('Vendor data not found. May have been already deleted or moved.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            try {
                                $record->refresh();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Failed')
                                    ->body('Cannot access vendor data. May have been deleted by another user.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            // Double check for associations
                            $usageDetails = $record->usage_details;

                            if ($record->usage_status === 'In Use') {
                                $details = [];
                                if ($usageDetails['productCount'] > 0) {
                                    $details[] = "{$usageDetails['productCount']} product(s)";
                                }
                                if ($usageDetails['expenseCount'] > 0) {
                                    $details[] = "{$usageDetails['expenseCount']} expense(s)";
                                }
                                if ($usageDetails['notaDinasCount'] > 0) {
                                    $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                                }

                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Not Allowed')
                                    ->body("Vendor '{$record->name}' cannot be deleted because it is being used in ".implode(' and ', $details).'. Please remove these associations first.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }

                            try {
                                $vendorName = $record->name ?? 'Unknown Vendor';
                                $record->delete();

                                Notification::make()
                                    ->success()
                                    ->title('Vendor Successfully Deleted')
                                    ->body("'{$vendorName}' has been deleted from the system.")
                                    ->duration(5000)
                                    ->send();

                                return true;

                            } catch (QueryException $e) {
                                $errorCode = $e->getCode();
                                if ($errorCode === '23000') {
                                    Notification::make()
                                        ->danger()
                                        ->title('Deletion Failed - Data Constraint')
                                        ->body('This vendor cannot be deleted because it is referenced by other data in the system.')
                                        ->persistent()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->danger()
                                        ->title('Database Error')
                                        ->body('A database error occurred while deleting the vendor. Please try again later.')
                                        ->persistent()
                                        ->send();
                                }

                                return false;

                            } catch (ModelNotFoundException $e) {
                                Notification::make()
                                    ->warning()
                                    ->title('Vendor Already Deleted')
                                    ->body('This vendor appears to have been already deleted by another user.')
                                    ->send();

                                return false;

                            } catch (Exception $e) {
                                Log::error('Vendor deletion failed', [
                                    'vendor_id' => $record->id ?? 'unknown',
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                ]);

                                Notification::make()
                                    ->danger()
                                    ->title('Unexpected Error')
                                    ->body('An unexpected error occurred while deleting the vendor. System administrator has been notified.')
                                    ->persistent()
                                    ->send();

                                return false;
                            }
                        }),

                    Action::make('cannot_delete')
                        ->label('Cannot Delete')
                        ->icon('heroicon-m-shield-exclamation')
                        ->color('gray')
                        ->tooltip('This vendor cannot be deleted because it is being used')
                        ->visible(function (Vendor $record): bool {
                            return $record->usage_status === 'In Use';
                        })
                        ->action(function (Vendor $record) {
                            $usageDetails = $record->usage_details;

                            $details = [];
                            if ($usageDetails['productCount'] > 0) {
                                $details[] = "{$usageDetails['productCount']} product(s)";
                            }
                            if ($usageDetails['expenseCount'] > 0) {
                                $details[] = "{$usageDetails['expenseCount']} expense(s)";
                            }
                            if ($usageDetails['notaDinasCount'] > 0) {
                                $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                            }
                            if ($usageDetails['productPenambahanCount'] > 0) {
                                $details[] = "{$usageDetails['productPenambahanCount']} product addition(s)";
                            }

                            Notification::make()
                                ->warning()
                                ->title('Cannot Delete Vendor')
                                ->body("'{$record->name}' cannot be deleted because it has associated ".implode(' and ', $details).'. Please remove these associations first.')
                                ->persistent()
                                ->send();
                        }),
                    Action::make('view_usage')
                        ->label('View Usage')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading(fn (Vendor $record) => 'Usage Details for: '.$record->name)
                        ->modalDescription('See where this vendor is currently being used')
                        ->modalContent(function (Vendor $record) {
                            $usageDetails = $record->usage_details;
                            $productCount = $usageDetails['productCount'];
                            $expenseCount = $usageDetails['expenseCount'];

                            $content = '<div class="space-y-4">';

                            if ($productCount > 0) {
                                $products = $record->productVendors()
                                    ->with('product')
                                    ->get()
                                    ->groupBy('product.name');

                                $content .= '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-yellow-800 mb-2">Used in Products ('.$productCount.' items)</h3>';
                                $content .= '<ul class="list-disc list-inside text-yellow-700 space-y-1">';

                                foreach ($products as $productName => $items) {
                                    $totalQty = $items->sum('quantity');
                                    $content .= '<li>'.$productName.' (Quantity: '.$totalQty.')</li>';
                                }

                                $content .= '</ul></div>';
                            }

                            if ($expenseCount > 0) {
                                $content .= '<div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-blue-800 mb-2">Related Expenses</h3>';
                                $content .= '<p class="text-blue-700">'.$expenseCount.' expense transaction(s) are associated with this vendor.</p>';
                                $content .= '</div>';
                            }

                            $productPenambahanCount = $usageDetails['productPenambahanCount'];
                            if ($productPenambahanCount > 0) {
                                $productPenambahans = $record->productPenambahans()
                                    ->with('product')
                                    ->get()
                                    ->groupBy('product.name');

                                $content .= '<div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-purple-800 mb-2">Used in Product Additions ('.$productPenambahanCount.' items)</h3>';
                                $content .= '<ul class="list-disc list-inside text-purple-700 space-y-1">';

                                foreach ($productPenambahans as $productName => $items) {
                                    $totalAmount = $items->sum('harga_publish');
                                    $content .= '<li>'.$productName.' (Total: Rp '.number_format($totalAmount, 0, ',', '.').')</li>';
                                }

                                $content .= '</ul></div>';
                            }

                            $totalUsage = $productCount + $expenseCount + $usageDetails['notaDinasCount'] + $productPenambahanCount;
                            if ($totalUsage === 0) {
                                $content .= '<div class="p-4 bg-green-50 border border-green-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-green-800 mb-2">No Usage Found</h3>';
                                $content .= '<p class="text-green-700">This vendor is not currently used in any products, expenses, nota dinas, or product additions and can be safely deleted.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('view_products')
                        ->label('View Products')
                        ->icon('heroicon-o-shopping-bag')
                        ->color('success')
                        ->modalHeading(fn (Vendor $record) => 'Products using: '.$record->name)
                        ->modalDescription('Detailed list of all products that use this vendor')
                        ->visible(fn (Vendor $record) => $record->productVendors()->count() > 0)
                        ->modalContent(function (Vendor $record) {
                            $productVendors = $record->productVendors()
                                ->with(['product.category'])
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $content = '<div class="space-y-4">';

                            if ($productVendors->count() > 0) {
                                $content .= '<div class="p-4 bg-green-50 border border-green-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-green-800 mb-4">Products List ('.$productVendors->count().' entries)</h3>';

                                // Group by product
                                $groupedProducts = $productVendors->groupBy('product.name');

                                foreach ($groupedProducts as $productName => $items) {
                                    $product = $items->first()->product;
                                    $totalQuantity = $items->sum('quantity');

                                    $content .= '<div class="mb-4 p-3 bg-white border border-green-300 rounded-lg">';
                                    $content .= '<div class="flex justify-between items-start mb-2">';
                                    $content .= '<h4 class="font-medium text-green-900">'.$productName.'</h4>';
                                    $content .= '<span class="text-sm text-green-600 bg-green-100 px-2 py-1 rounded">Total Qty: '.$totalQuantity.'</span>';
                                    $content .= '</div>';

                                    if ($product && $product->category) {
                                        $content .= '<p class="text-sm text-green-700 mb-2"><strong>Category:</strong> '.$product->category->name.'</p>';
                                    }

                                    // Detail per entry
                                    $content .= '<div class="text-sm text-green-600">';
                                    $content .= '<strong>Usage Details:</strong>';
                                    $content .= '<ul class="list-disc list-inside mt-1 ml-2">';

                                    foreach ($items as $item) {
                                        $content .= '<li>Quantity: '.$item->quantity;
                                        if ($item->price) {
                                            $content .= ' | Price: Rp '.number_format($item->price, 0, ',', '.');
                                        }
                                        if ($item->created_at) {
                                            $content .= ' | Added: '.$item->created_at->format('d M Y');
                                        }
                                        $content .= '</li>';
                                    }

                                    $content .= '</ul>';
                                    $content .= '</div>';
                                    $content .= '</div>';
                                }
                            } else {
                                $content .= '<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">';
                                $content .= '<p class="text-gray-600">This vendor is not used in any products.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('view_expenses')
                        ->label('View Expenses')
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        ->modalHeading(fn (Vendor $record) => 'Expenses for: '.$record->name)
                        ->modalDescription('Detailed list of all expenses related to this vendor')
                        ->visible(fn (Vendor $record) => $record->usage_details['expenseCount'] > 0)
                        ->modalContent(function (Vendor $record) {
                            $expenses = $record->expenses()
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $content = '<div class="space-y-4">';

                            if ($expenses->count() > 0) {
                                $totalAmount = $expenses->sum('amount');

                                $content .= '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-yellow-800 mb-4">Expenses List ('.$expenses->count().' transactions)</h3>';
                                $content .= '<p class="text-yellow-700 mb-4"><strong>Total Amount:</strong> Rp '.number_format($totalAmount, 0, ',', '.').'</p>';

                                foreach ($expenses as $expense) {
                                    $content .= '<div class="mb-3 p-3 bg-white border border-yellow-300 rounded-lg">';
                                    $content .= '<div class="flex justify-between items-start mb-2">';
                                    $content .= '<h4 class="font-medium text-yellow-900">'.($expense->description ?? 'No Description').'</h4>';
                                    $content .= '<span class="text-sm text-yellow-600 bg-yellow-100 px-2 py-1 rounded">Rp '.number_format($expense->amount, 0, ',', '.').'</span>';
                                    $content .= '</div>';

                                    $content .= '<div class="text-sm text-yellow-600 space-y-1">';
                                    if ($expense->transaction_date) {
                                        $content .= '<p><strong>Date:</strong> '.Carbon::parse($expense->transaction_date)->format('d M Y').'</p>';
                                    }
                                    if ($expense->category_uang_keluar) {
                                        $content .= '<p><strong>Category:</strong> '.ucfirst(str_replace('_', ' ', $expense->category_uang_keluar)).'</p>';
                                    }
                                    if ($expense->created_at) {
                                        $content .= '<p><strong>Recorded:</strong> '.$expense->created_at->format('d M Y H:i').'</p>';
                                    }
                                    $content .= '</div>';
                                    $content .= '</div>';
                                }
                            } else {
                                $content .= '<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">';
                                $content .= '<p class="text-gray-600">This vendor has no related expenses.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('view_nota_dinas')
                        ->label('View Nota Dinas')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->modalHeading(fn (Vendor $record) => 'Nota Dinas for: '.$record->name)
                        ->modalDescription('Detailed list of all nota dinas details related to this vendor')
                        ->visible(fn (Vendor $record) => $record->notaDinasDetails()->count() > 0)
                        ->modalContent(function (Vendor $record) {
                            $notaDinasDetails = $record->notaDinasDetails()
                                ->with('notaDinas')
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $content = '<div class="space-y-4">';

                            if ($notaDinasDetails->count() > 0) {
                                $totalAmount = $notaDinasDetails->sum('jumlah_transfer');

                                $content .= '<div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">';
                                $content .= '<h3 class="font-semibold text-blue-800 mb-4">Nota Dinas Details ('.$notaDinasDetails->count().' entries)</h3>';
                                $content .= '<p class="text-blue-700 mb-4"><strong>Total Transfer Amount:</strong> Rp '.number_format($totalAmount, 0, ',', '.').'</p>';

                                foreach ($notaDinasDetails as $detail) {
                                    $content .= '<div class="mb-3 p-3 bg-white border border-blue-300 rounded-lg">';
                                    $content .= '<div class="flex justify-between items-start mb-2">';
                                    $content .= '<h4 class="font-medium text-blue-900">'.($detail->keperluan ?? 'No Description').'</h4>';
                                    $content .= '<span class="text-sm text-blue-600 bg-blue-100 px-2 py-1 rounded">Rp '.number_format($detail->jumlah_transfer, 0, ',', '.').'</span>';
                                    $content .= '</div>';

                                    $content .= '<div class="text-sm text-blue-600 space-y-1">';
                                    if ($detail->event) {
                                        $content .= '<p><strong>Event:</strong> '.$detail->event.'</p>';
                                    }
                                    if ($detail->invoice_number) {
                                        $content .= '<p><strong>Invoice:</strong> '.$detail->invoice_number.'</p>';
                                    }
                                    if ($detail->status_invoice) {
                                        $statusLabel = ucfirst(str_replace('_', ' ', $detail->status_invoice));
                                        $statusColor = match ($detail->status_invoice) {
                                            'sudah_dibayar' => 'text-green-600',
                                            'menunggu' => 'text-yellow-600',
                                            'belum_dibayar' => 'text-red-600',
                                            default => 'text-blue-600'
                                        };
                                        $content .= '<p><strong>Status:</strong> <span class="'.$statusColor.'">'.$statusLabel.'</span></p>';
                                    }
                                    if ($detail->payment_stage) {
                                        $content .= '<p><strong>Payment Stage:</strong> '.ucfirst(str_replace('_', ' ', $detail->payment_stage)).'</p>';
                                    }
                                    if ($detail->jenis_pengeluaran) {
                                        $content .= '<p><strong>Type:</strong> '.ucfirst(str_replace('_', ' ', $detail->jenis_pengeluaran)).'</p>';
                                    }
                                    if ($detail->created_at) {
                                        $content .= '<p><strong>Created:</strong> '.$detail->created_at->format('d M Y H:i').'</p>';
                                    }
                                    $content .= '</div>';
                                    $content .= '</div>';
                                }
                            } else {
                                $content .= '<div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">';
                                $content .= '<p class="text-gray-600">This vendor has no related nota dinas details.</p>';
                                $content .= '</div>';
                            }

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                    Action::make('duplicate')
                        ->label('Duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Duplicate Vendor')
                        ->modalDescription('Are you sure you want to duplicate this vendor? The name and slug will be modified to ensure uniqueness.')
                        ->modalSubmitActionLabel('Yes, duplicate')
                        ->action(function (Vendor $record) {
                            $attributesToDuplicate = $record->getAttributes();
                            unset(
                                $attributesToDuplicate['id'],
                                $attributesToDuplicate['slug'], // Slug will be regenerated
                                $attributesToDuplicate['created_at'],
                                $attributesToDuplicate['updated_at'],
                                $attributesToDuplicate['deleted_at']
                            );

                            $newVendor = new Vendor($attributesToDuplicate);
                            $newVendor->name = $record->name.' (Copy)';

                            // Generate unique slug
                            $baseSlug = Str::slug($newVendor->name);
                            $newSlug = $baseSlug;
                            $counter = 1;
                            while (Vendor::where('slug', $newSlug)->exists()) {
                                $newSlug = $baseSlug.'-'.$counter++;
                            }
                            $newVendor->slug = $newSlug;
                            $newVendor->save();

                            Notification::make()
                                ->title('Vendor Duplicated')
                                ->body("Vendor '{$record->name}' has been successfully duplicated as '{$newVendor->name}'.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    ForceDeleteAction::make()
                        ->requiresConfirmation(),
                    RestoreAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Vendors')
                        ->modalDescription('Are you sure you want to delete the selected vendors? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete selected')
                        ->action(function (Collection $records) {
                            $deletedCount = 0;
                            $protectedVendors = [];
                            $errorVendors = [];

                            foreach ($records as $vendor) {
                                try {
                                    // Check if vendor can be deleted
                                    if ($vendor->usage_status === 'In Use') {
                                        $usageDetails = $vendor->usage_details;
                                        $details = [];
                                        if ($usageDetails['productCount'] > 0) {
                                            $details[] = "{$usageDetails['productCount']} product(s)";
                                        }
                                        if ($usageDetails['expenseCount'] > 0) {
                                            $details[] = "{$usageDetails['expenseCount']} expense(s)";
                                        }
                                        if ($usageDetails['notaDinasCount'] > 0) {
                                            $details[] = "{$usageDetails['notaDinasCount']} nota dinas detail(s)";
                                        }
                                        if ($usageDetails['productPenambahanCount'] > 0) {
                                            $details[] = "{$usageDetails['productPenambahanCount']} product addition(s)";
                                        }
                                        $protectedVendors[] = "• {$vendor->name}: ".implode(', ', $details);

                                        continue;
                                    }

                                    // Attempt to delete
                                    $vendor->delete();
                                    $deletedCount++;

                                } catch (Exception $e) {
                                    $errorVendors[] = "{$vendor->name}: {$e->getMessage()}";
                                }
                            }

                            // Show results
                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Vendors Deleted')
                                    ->body("{$deletedCount} vendor(s) have been successfully deleted.")
                                    ->send();
                            }

                            if (! empty($protectedVendors)) {
                                Notification::make()
                                    ->warning()
                                    ->title('Some Vendors Could Not Be Deleted')
                                    ->body("The following vendors cannot be deleted because they are being used:\n\n".implode("\n", $protectedVendors)."\n\nPlease remove these associations first.")
                                    ->persistent()
                                    ->send();
                            }

                            if (! empty($errorVendors)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Deletion Errors')
                                    ->body("Errors occurred while deleting some vendors:\n\n".implode("\n", $errorVendors))
                                    ->persistent()
                                    ->send();
                            }

                            if ($deletedCount === 0 && empty($protectedVendors) && empty($errorVendors)) {
                                Notification::make()
                                    ->info()
                                    ->title('No Action Taken')
                                    ->body('No valid data found for deletion.')
                                    ->send();
                            }
                        }),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateHeading('No vendors yet')
            ->emptyStateDescription('Create your first vendor to get started.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create vendor')
                    ->url(route('filament.admin.resources.vendors.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->poll('60s');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Vendor Information')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Section::make('Vendor Identity')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label('Vendor Name'),

                                                TextEntry::make('category.name')
                                                    ->label('Category'),

                                                TextEntry::make('status')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'vendor' => 'primary',
                                                        'product' => 'success',
                                                        default => 'gray',
                                                    })
                                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                                        'vendor' => 'Vendor',
                                                        'product' => 'Product',
                                                        default => ucfirst($state),
                                                    }),

                                                TextEntry::make('pic_name')
                                                    ->label('PIC Name'),

                                                TextEntry::make('phone')
                                                    ->label('Phone Number')
                                                    ->formatStateUsing(fn (string $state) => '+62 '.$state),

                                                TextEntry::make('address')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                Section::make('Business Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('description')
                                                    ->markdown()
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Financial Information')
                            ->icon('heroicon-m-currency-dollar')
                            ->schema([
                                Section::make('Pricing Details')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('harga_publish')
                                                    ->label('Published Price')
                                                    ->money('IDR'),

                                                TextEntry::make('harga_vendor')
                                                    ->label('Vendor Price')
                                                    ->money('IDR'),

                                                TextEntry::make('profit_amount')
                                                    ->label('Profit Amount')
                                                    ->state(function (Vendor $record): float {
                                                        return $record->harga_publish - $record->harga_vendor;
                                                    })
                                                    ->money('IDR')
                                                    ->color(fn (Vendor $record): string => ($record->harga_publish - $record->harga_vendor) > 0 ? 'success' : 'danger'
                                                    ),

                                                TextEntry::make('profit_margin')
                                                    ->label('Profit Margin')
                                                    ->state(function (Vendor $record): float {
                                                        if ($record->harga_publish > 0) {
                                                            return (($record->harga_publish - $record->harga_vendor) / $record->harga_publish) * 100;
                                                        }

                                                        return 0;
                                                    })
                                                    ->suffix('%')
                                                    ->numeric(2),
                                            ]),
                                    ]),

                                Section::make('Banking Information')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('bank_name')
                                                    ->label('Bank Name')
                                                    ->prefix('Bank '),

                                                TextEntry::make('bank_account')
                                                    ->label('Account Number'),

                                                TextEntry::make('account_holder')
                                                    ->label('Account Holder'),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Documents')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Section::make('Contract Documents')
                                    ->schema([
                                        Grid::make(1)
                                            ->schema([
                                                ViewEntry::make('kontrak_kerjasama')
                                                    ->label('Partnership Agreement')
                                                    ->view('filament.infolists.components.file-view')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Usage Information')
                            ->icon('heroicon-m-chart-bar')
                            ->schema([
                                Section::make('Vendor Usage Overview')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('products_count')
                                                    ->label('Used in Products')
                                                    ->state(function (Vendor $record): int {
                                                        $basicFacilitiesCount = $record->productVendors()->count();
                                                        $additionsCount = $record->productPenambahans()->count();

                                                        return $basicFacilitiesCount + $additionsCount;
                                                    })
                                                    ->badge()
                                                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'success')
                                                    ->suffix(' items')
                                                    ->tooltip(function (Vendor $record): string {
                                                        $basicCount = $record->productVendors()->count();
                                                        $additionsCount = $record->productPenambahans()->count();
                                                        $details = [];
                                                        if ($basicCount > 0) {
                                                            $details[] = "{$basicCount} in Basic Facilities";
                                                        }
                                                        if ($additionsCount > 0) {
                                                            $details[] = "{$additionsCount} in Additions";
                                                        }

                                                        return ! empty($details) ? implode(', ', $details) : 'No usage';
                                                    }),

                                                TextEntry::make('expenses_count')
                                                    ->label('Related Expenses')
                                                    ->state(function (Vendor $record): int {
                                                        return $record->usage_details['expenseCount'];
                                                    })
                                                    ->badge()
                                                    ->color(fn (int $state): string => $state > 0 ? 'info' : 'gray')
                                                    ->suffix(' transactions'),

                                                TextEntry::make('deletion_status')
                                                    ->label('Deletion Status')
                                                    ->state(function (Vendor $record): string {
                                                        return $record->usage_status === 'In Use' ? 'Protected' : 'Can be deleted';
                                                    })
                                                    ->badge()
                                                    ->color(function (Vendor $record): string {
                                                        return $record->usage_status === 'In Use' ? 'danger' : 'success';
                                                    }),
                                            ]),
                                    ]),

                                Section::make('Usage Details')
                                    ->schema([
                                        TextEntry::make('usage_details')
                                            ->label('Detailed Usage Information')
                                            ->state(function (Vendor $record): string {
                                                $usageDetails = $record->usage_details;
                                                $productCount = $usageDetails['productCount'];
                                                $expenseCount = $usageDetails['expenseCount'];
                                                $productPenambahanCount = $usageDetails['productPenambahanCount'];

                                                $details = [];

                                                // Basic Facilities Products
                                                if ($productCount > 0) {
                                                    $productNames = $record->productVendors()
                                                        ->with('product')
                                                        ->get()
                                                        ->pluck('product.name')
                                                        ->unique()
                                                        ->take(5);

                                                    $details[] = "**Product Basic Facilities** ({$productCount} total): ".$productNames->implode(', ').
                                                        ($productCount > 5 ? ' and '.($productCount - 5).' more...' : '');
                                                }

                                                // Product Additions
                                                if ($productPenambahanCount > 0) {
                                                    $additionProducts = $record->productPenambahans()
                                                        ->with('product')
                                                        ->get()
                                                        ->pluck('product.name')
                                                        ->unique()
                                                        ->take(5);

                                                    $details[] = "**Product Additions** ({$productPenambahanCount} total): ".$additionProducts->implode(', ').
                                                        ($productPenambahanCount > 5 ? ' and '.($productPenambahanCount - 5).' more...' : '');
                                                }

                                                if ($expenseCount > 0) {
                                                    $details[] = "**Expenses**: {$expenseCount} transaction(s)";
                                                }

                                                if (empty($details)) {
                                                    return 'This vendor is not currently used in any products, expenses, or product additions and can be safely deleted.';
                                                }

                                                return implode("\n\n", $details).
                                                    "\n\n**Note**: This vendor cannot be deleted while these associations exist.";
                                            })
                                            ->markdown()
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull(),
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
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'view' => ViewVendor::route('/{record}'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Data vendor yang telah dibuat dan dikelola';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'pic_name',
            'phone',
            'bank_name',
            'bank_account',
            'account_holder',
            'address',
            'status',
            'category.name',
        ];
    }

    protected static function calculateProfitMetrics(Set $set, Get $get): void
    {
        try {
            $publishPrice = (float) str_replace([',', '.'], '', $get('harga_publish') ?? '0');
            $vendorPrice = (float) str_replace([',', '.'], '', $get('harga_vendor') ?? '0');

            if ($publishPrice > 0) {
                $profit = $publishPrice - $vendorPrice;
                $margin = ($profit / $publishPrice) * 100;

                $set('profit_amount', $profit);
                $set('profit_margin', round($margin, 2));
            } else {
                $set('profit_amount', 0);
                $set('profit_margin', 0);
            }
        } catch (Exception $e) {
            // Set default values if calculation fails
            $set('profit_amount', 0);
            $set('profit_margin', 0);
        }
    }

    public static function getModelLabel(): string
    {
        return __('Vendor');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Vendors');
    }

    public static function getNavigationLabel(): string
    {
        return __('Vendors');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::mutateFormData($data);
    }

    protected static function mutateFormData(array $data): array
    {
        // Clean up phone number format with better handling
        if (isset($data['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $data['phone']);
            // Handle if phone starts with 62
            if (str_starts_with($phone, '62')) {
                $phone = substr($phone, 2);
            }
            // Handle if phone starts with 0
            if (str_starts_with($phone, '0')) {
                $phone = substr($phone, 1);
            }
            $data['phone'] = $phone;
        }

        // Better price handling
        if (isset($data['harga_publish'])) {
            $data['harga_publish'] = (float) str_replace([',', '.'], '', $data['harga_publish']);
        }
        if (isset($data['harga_vendor'])) {
            $data['harga_vendor'] = (float) str_replace([',', '.'], '', $data['harga_vendor']);
        }

        // Handle empty strings for numeric fields
        if (empty($data['stock'])) {
            $data['stock'] = 0;
        }

        // Clean up bank account with better handling
        if (isset($data['bank_account'])) {
            $data['bank_account'] = preg_replace('/[^0-9]/', '', $data['bank_account']);
            if (empty($data['bank_account'])) {
                unset($data['bank_account']); // Remove if empty instead of saving empty string
            }
        }

        return $data;
    }

    public static function getNavigationSortOrder(): int
    {
        return 1;
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-building-storefront';
    }

    protected function mutateFormDataBeforeUpdate(array $data): array
    {
        try {
            $mutatedData = static::mutateFormData($data);

            // Log the before and after data
            Log::info('Vendor Update - Original Data:', $data);
            Log::info('Vendor Update - Mutated Data:', $mutatedData);

            return $mutatedData;
        } catch (Exception $e) {
            Log::error('Vendor Update Error:', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }
}
