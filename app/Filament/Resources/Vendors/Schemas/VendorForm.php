<?php

namespace App\Filament\Resources\Vendors\Schemas;

use App\Models\Category;
use App\Models\Vendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
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
                                            ->live(onBlur: true),
                                        TextInput::make('slug')
                                            ->default(null)
                                            ->disabled()
                                            ->dehydrated(false),
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
                                        Toggle::make('is_master')
                                            ->label('Master')
                                            ->helperText('Tandai vendor ini sebagai data master')
                                            ->reactive()
                                            ->default(false),
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
                                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                                                ->maxSize(2048)
                                                ->downloadable()
                                                ->openable()
                                                ->helperText('Upload PDF file (max 2MB) or image file (max 2MB)')
                                                ->columnSpanFull(),
                                    ]),
                            ]),

                        
                        Tab::make('Usage Information')
                            ->icon('heroicon-m-chart-bar')
                            ->schema([
                                Section::make('Vendor Usage Overview')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                ViewField::make('products_count')
                                                    ->label('Used in Products')
                                                    ->view('filament.forms.components.text-content')
                                                    ->formatStateUsing(function ($record): string {
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

                                                ViewField::make('expenses_count')
                                                    ->label('Related Expenses')
                                                    ->view('filament.forms.components.text-content')
                                                    ->formatStateUsing(function ($record): string {
                                                        if (! $record) {
                                                            return '0 transactions';
                                                        }
                                                        $count = (int) ($record->usage_details['expenseCount'] ?? 0);
                                                        return $count.' transactions';
                                                    }),

                                                ViewField::make('deletion_status')
                                                    ->label('Deletion Status')
                                                    ->view('filament.forms.components.text-content')
                                                    ->formatStateUsing(function ($record): string {
                                                        if (! $record) {
                                                            return 'Unknown';
                                                        }
                                                        return $record->usage_status === 'In Use' ? 'Protected' : 'Can be deleted';
                                                    }),
                                            ]),
                                    ]),

                                Section::make('Usage Details')
                                    ->schema([
                                        ViewField::make('usage_summary')
                                            ->label('Detailed Usage Information')
                                            ->view('filament.forms.components.text-content')
                                            ->formatStateUsing(function ($record): string {
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
                                        ViewField::make('usage_note')
                                            ->label('Catatan')
                                            ->view('filament.forms.components.text-content')
                                            ->formatStateUsing(fn () => 'Catatan: Vendor ini tidak dapat dihapus selama masih terhubung dengan data lain.')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),
                            ])
                    ])
                    ->columnSpanFull(),
                Section::make('Pengaturan Master')
                    ->schema([
                        Repeater::make('priceHistories')
                            ->relationship('priceHistories')
                            ->label('Periode Harga')
                            ->addActionLabel('Tambah periode harga')
                            ->grid(2)
                            ->columns(2)
                            ->collapsible()
                            ->schema([
                                DatePicker::make('effective_from')
                                    ->label('Tgl Mulai')
                                    ->required()
                                    ->native(false)
                                    ->format('Y-m-d')
                                    ->displayFormat('d / m / Y')
                                    ->reactive(),
                                DatePicker::make('effective_to')
                                    ->label('Tgl Akhir')
                                    ->required()
                                    ->native(false)
                                    ->format('Y-m-d')
                                    ->displayFormat('d / m / Y')
                                    ->reactive(),
                                TextInput::make('harga_publish')
                                    ->label('Harga Publish')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->debounce(500),

                                TextInput::make('harga_vendor')
                                    ->label('Harga Vendor')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->debounce(500),

                                TextInput::make('profit_amount')
                                    ->label('Profit')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->default(0),

                                TextInput::make('profit_margin')
                                    ->label('Profit Margin')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('%')
                                    ->default(0),

                                FileUpload::make('kontrak')
                                    ->label('Kontrak')
                                    ->directory('vendor-contracts/history')
                                    ->preserveFilenames()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),
                                TextInput::make('description')
                                    ->label('Deskripsi')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->hidden(fn (Get $get) => ! (bool) $get('is_master'))
                    ->columnSpanFull(),
            ]);
    }
}
