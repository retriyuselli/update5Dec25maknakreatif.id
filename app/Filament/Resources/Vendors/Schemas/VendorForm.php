<?php

namespace App\Filament\Resources\Vendors\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\RawJs;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Vendor Tabs')
                    ->tabs([
                        Tab::make('Informasi Dasar')
                            ->schema([
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug((string) $state))),
                                        TextInput::make('slug')
                                            ->default(null)
                                            ->reactive()
                                            ->afterStateHydrated(function ($component, $state, Get $get) {
                                                if (! $state) {
                                                    $component->state(Str::slug((string) ($get('name') ?? '')));
                                                }
                                            }),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->required()
                                            ->prefix('+62')
                                            ->placeholder('812XXXXXXXX')
                                            ->helperText('Enter number without leading zero'),
                                        TextInput::make('address')
                                            ->default(null),
                                        TextInput::make('pic_name')
                                            ->default(null),
                                        Select::make('status')
                                            ->options(['vendor' => 'Vendor', 'product' => 'Product'])
                                            ->default('product')
                                            ->required(),
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
                                                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', $state ? Str::slug($state) : '')),
                                                    TextInput::make('slug')
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->unique(Category::class, 'slug', ignoreRecord: true),
                                                    Toggle::make('is_active')
                                                        ->required(),
                                                        ]),
                                        RichEditor::make('description')
                                            ->columnSpanFull()
                                            ->minLength(10)
                                            ->required()
                                            ->label('Description'),
                                    ]),
                            ]),

                        Tab::make('Financial Information')
                            ->schema([
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('harga_publish')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->default(0.0),
                                        TextInput::make('harga_vendor')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->default(0.0),
                                        TextInput::make('profit_amount')
                                            ->required()
                                            ->numeric()
                                            ->readOnly()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->default(0.0),
                                        TextInput::make('profit_margin')
                                            ->required()
                                            ->numeric()
                                            ->prefix('%')
                                            ->default(0.0),
                                        TextInput::make('stock')
                                            ->numeric()
                                            ->default(10),
                                    ]),
                            ]),

                        Tab::make('Banking Information')
                            ->schema([
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('bank_name')
                                                ->label('Bank Name')
                                                ->prefix('Bank '),

                                            TextInput::make('bank_account')
                                                ->label('Account Number')
                                                ->numeric(),

                                            TextInput::make('account_holder')
                                                ->label('Account Holder Name'),

                                            FileUpload::make('kontrak_kerjasama')
                                                ->label('Partnership Agreement')
                                                ->directory('vendor-contracts')
                                                ->preserveFilenames()
                                                ->acceptedFileTypes(['application/pdf'])
                                                ->maxSize(10240)
                                                ->downloadable()
                                                ->openable()
                                                ->helperText('Upload PDF file (max 10MB)')
                                                ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Pricing History')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                Section::make('Pricing History')
                                    ->description('Kelola riwayat harga vendor berdasarkan periode efektif')
                                    ->schema([
                                        Repeater::make('priceHistories')
                                            ->relationship('priceHistories')
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->grid(2)
                                            ->schema([
                                                DatePicker::make('effective_from')
                                                    ->label('Effective From')
                                                    ->required(),
                                                DatePicker::make('effective_to')
                                                    ->label('Effective To')
                                                    ->helperText('Kosongkan jika berlaku sampai perubahan berikutnya'),
                                                Select::make('status')
                                                    ->label('Status')
                                                    ->options([
                                                        'active' => 'Active',
                                                        'scheduled' => 'Scheduled',
                                                        'archived' => 'Archived',
                                                    ])
                                                    ->default('active')
                                                    ->required(),
                                                TextInput::make('harga_publish')
                                                    ->label('Published Price')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->required(),
                                                TextInput::make('harga_vendor')
                                                    ->label('Vendor Price')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->required(),
                                                TextInput::make('profit_amount')
                                                    ->label('Profit Amount')
                                                    ->prefix('Rp')
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 0, ',', '.')),
                                            TextInput::make('profit_margin')
                                                ->label('Profit Margin')
                                                ->suffix('%')
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 2)),
                                            ])
                                            ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                                $activeCount = collect($state ?? [])->filter(function ($item) {
                                                    return ($item['status'] ?? null) === 'active';
                                                })->count();
                                                if ($activeCount > 1) {
                                                    Notification::make()
                                                        ->title('Duplikasi Status Active')
                                                        ->body('Hanya satu status active yang diperbolehkan pada riwayat harga.')
                                                        ->danger()
                                                        ->send();
                                                }
                                                $active = collect($state ?? [])->first(function ($item) {
                                                    return ($item['status'] ?? null) === 'active';
                                                });
                                                if ($active) {
                                                    $hp = (float) preg_replace('/[^0-9.]/', '', (string) ($active['harga_publish'] ?? 0));
                                                    $hv = (float) preg_replace('/[^0-9.]/', '', (string) ($active['harga_vendor'] ?? 0));
                                                    $profit = $hp - $hv;
                                                    $margin = $hp > 0 ? round(($profit / $hp) * 100, 2) : 0;

                                                    $set('harga_publish', $hp);
                                                    $set('harga_vendor', $hv);
                                                    $set('profit_amount', $profit);
                                                    $set('profit_margin', $margin);
                                                }
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                $activeCount = collect($state ?? [])->filter(function ($item) {
                                                    return ($item['status'] ?? null) === 'active';
                                                })->count();
                                                if ($activeCount > 1) {
                                                    Notification::make()
                                                        ->title('Duplikasi Status Active')
                                                        ->body('Hanya satu status active yang diperbolehkan pada riwayat harga.')
                                                        ->danger()
                                                        ->send();
                                                }
                                                $active = collect($state ?? [])->first(function ($item) {
                                                    return ($item['status'] ?? null) === 'active';
                                                });
                                                if ($active) {
                                                    $hp = (float) preg_replace('/[^0-9.]/', '', (string) ($active['harga_publish'] ?? 0));
                                                    $hv = (float) preg_replace('/[^0-9.]/', '', (string) ($active['harga_vendor'] ?? 0));
                                                    $profit = $hp - $hv;
                                                    $margin = $hp > 0 ? round(($profit / $hp) * 100, 2) : 0;

                                                    $set('harga_publish', $hp);
                                                    $set('harga_vendor', $hv);
                                                    $set('profit_amount', $profit);
                                                    $set('profit_margin', $margin);
                                                }
                                            })
                                            ->addActionLabel('Tambah Riwayat Harga')
                                            ->itemLabel(fn (array $state) => 'Harga mulai '.($state['effective_from'] ?? '-')),
                                    ])
                                    ->collapsible(),
                            ]),
                        
                        Tab::make('Usage Information')
                            ->icon('heroicon-m-chart-bar')
                            ->schema([
                                Section::make('Vendor Usage Overview')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Placeholder::make('products_count')
                                                    ->label('Used in Products')
                                                    ->content(function ($record): string {
                                                        if (! $record) {
                                                            return '0 items';
                                                        }
                                                        $basicFacilitiesCount = $record->productVendors()->count();
                                                        $additionsCount = $record->productPenambahans()->count();
                                                        $total = $basicFacilitiesCount + $additionsCount;
                                                        return $total.' items';
                                                    })
                                                    ->helperText(function ($record): string {
                                                        if (! $record) {
                                                            return 'No usage';
                                                        }
                                                        $basicCount = $record->productVendors()->count();
                                                        $additionsCount = $record->productPenambahans()->count();
                                                        $details = [];
                                                        if ($basicCount > 0) {
                                                            $details[] = $basicCount.' in Basic Facilities';
                                                        }
                                                        if ($additionsCount > 0) {
                                                            $details[] = $additionsCount.' in Additions';
                                                        }
                                                        return ! empty($details) ? implode(', ', $details) : 'No usage';
                                                    }),

                                                Placeholder::make('expenses_count')
                                                    ->label('Related Expenses')
                                                    ->content(function ($record): string {
                                                        if (! $record) {
                                                            return '0 transactions';
                                                        }
                                                        $count = (int) ($record->usage_details['expenseCount'] ?? 0);
                                                        return $count.' transactions';
                                                    }),

                                                Placeholder::make('deletion_status')
                                                    ->label('Deletion Status')
                                                    ->content(function ($record): string {
                                                        if (! $record) {
                                                            return 'Unknown';
                                                        }
                                                        return $record->usage_status === 'In Use' ? 'Protected' : 'Can be deleted';
                                                    }),
                                            ]),
                                    ]),

                                Section::make('Usage Details')
                                    ->schema([
                                        Placeholder::make('usage_summary')
                                            ->label('Detailed Usage Information')
                                            ->content(function ($record): string {
                                                if (! $record) {
                                                    return 'This vendor is not currently used in any products, expenses, or product additions and can be safely deleted.';
                                                }
                                                $usageDetails = $record->usage_details;
                                                $productCount = (int) ($usageDetails['productCount'] ?? 0);
                                                $expenseCount = (int) ($usageDetails['expenseCount'] ?? 0);
                                                $productPenambahanCount = (int) ($usageDetails['productPenambahanCount'] ?? 0);

                                                $lines = [];

                                                if ($productCount > 0) {
                                                    $productNames = $record->productVendors()
                                                        ->with('product')
                                                        ->get()
                                                        ->pluck('product.name')
                                                        ->filter()
                                                        ->unique()
                                                        ->values();

                                                    $topProducts = $productNames->take(5);
                                                    $line = 'Product Basic Facilities ('.$productCount.' total): '.$topProducts->implode(', ');
                                                    if ($productCount > 5) {
                                                        $line .= ' and '.($productCount - 5).' more...';
                                                    }
                                                    $lines[] = $line;
                                                }

                                                if ($productPenambahanCount > 0) {
                                                    $additionProducts = $record->productPenambahans()
                                                        ->with('product')
                                                        ->get()
                                                        ->pluck('product.name')
                                                        ->filter()
                                                        ->unique()
                                                        ->values();

                                                    $topAdditions = $additionProducts->take(5);
                                                    $line = 'Product Additions ('.$productPenambahanCount.' total): '.$topAdditions->implode(', ');
                                                    if ($productPenambahanCount > 5) {
                                                        $line .= ' and '.($productPenambahanCount - 5).' more...';
                                                    }
                                                    $lines[] = $line;
                                                }

                                                if ($expenseCount > 0) {
                                                    $lines[] = 'Expenses: '.$expenseCount.' transaction(s)';
                                                }

                                                $notaDinasCount = (int) ($usageDetails['notaDinasCount'] ?? 0);
                                                if ($notaDinasCount > 0) {
                                                    $ndNos = $record->notaDinasDetails()
                                                        ->with('notaDinas')
                                                        ->get()
                                                        ->pluck('notaDinas.no_nd')
                                                        ->filter()
                                                        ->unique()
                                                        ->values();

                                                    $topNdNos = $ndNos->take(5);
                                                    $line = 'Nota Dinas Details: '.$notaDinasCount.' record(s)';
                                                    if ($topNdNos->count() > 0) {
                                                        $line .= ' (ND: '.$topNdNos->implode(', ').')';
                                                    }
                                                    if ($ndNos->count() > 5) {
                                                        $line .= ' and '.($ndNos->count() - 5).' more...';
                                                    }
                                                    $lines[] = $line;
                                                }

                                                if (empty($lines)) {
                                                    return 'This vendor is not currently used in any products, expenses, or product additions and can be safely deleted.';
                                                }

                                                return implode("\n\n", $lines);
                                            })
                                            ->columnSpanFull(),
                                        Placeholder::make('usage_note')
                                            ->label('Note')
                                            ->content('Note: This vendor cannot be deleted while these associations exist.')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
