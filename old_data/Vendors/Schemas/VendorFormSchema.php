<?php

namespace App\Filament\Resources\Vendors\Schemas;

use App\Models\Category;
use App\Models\Vendor;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Schema as DbSchema;
use Illuminate\Support\Str;

class VendorFormSchema
{
    public static function components(): array
    {
        return [
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
                                                ->afterStateUpdated(function ($state, Set $set, ?Vendor $record) {
                                                    if ($state === null) {
                                                        $set('slug', '');
                                                        return;
                                                    }
                                                    $slug = Str::slug($state);
                                                    $exists = Vendor::where('slug', $slug)
                                                        ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                                        ->exists();
                                                    if ($exists) {
                                                        $slug = $slug . '-' . now()->timestamp;
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
                                                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', $state ? Str::slug($state) : '')),
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
                                                ->placeholder('812XXXXXXXX')
                                                ->helperText('Enter number without leading zero'),

                                            TextInput::make('address')
                                                ->required(),

                                            Placeholder::make('active_harga_publish')
                                                ->label('Published Price (Active)')
                                                ->content(function (Get $get, ?Vendor $record): string {
                                                    $value = 0;
                                                    if ($record && (int) ($record->harga_publish ?? 0) > 0) {
                                                        $value = (int) $record->harga_publish;
                                                    } else {
                                                        $active = $record?->activePrice();
                                                        $value = (int) ($active?->harga_publish ?? 0);
                                                    }
                                                    return $value > 0 ? 'Rp '.number_format($value, 0, ',', '.') : '-';
                                                }),

                                            Placeholder::make('active_harga_vendor')
                                                ->label('Vendor Price (Active)')
                                                ->content(function (Get $get, ?Vendor $record): string {
                                                    $value = 0;
                                                    if ($record && (int) ($record->harga_vendor ?? 0) > 0) {
                                                        $value = (int) $record->harga_vendor;
                                                    } else {
                                                        $active = $record?->activePrice();
                                                        $value = (int) ($active?->harga_vendor ?? 0);
                                                    }
                                                    return $value > 0 ? 'Rp '.number_format($value, 0, ',', '.') : '-';
                                                }),
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
                                                ->numeric()
                                                ->required()
                                                ->prefix('Rp')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',')
                                                ->rules(['integer', 'min:0'])
                                                ->live(onBlur: true)
                                                ->reactive()
                                                ->afterStateHydrated(function ($component, $state, Get $get, ?Vendor $record) {
                                                    $existing = (int) ($record?->harga_publish ?? 0);
                                                    if ($existing > 0) {
                                                        $component->state((string) $existing);
                                                        return;
                                                    }
                                                    $active = $record?->activePrice();
                                                    if ($active && (int) ($active->harga_publish ?? 0) > 0) {
                                                        $component->state((string) (int) $active->harga_publish);
                                                    }
                                                })
                                                ,

                                            TextInput::make('harga_vendor')
                                                ->label('Vendor Price')
                                                ->numeric()
                                                ->required()
                                                ->default(0)
                                                ->prefix('Rp')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',')
                                                ->rules(['integer', 'min:0'])
                                                ->live(onBlur: true)
                                                ->reactive()
                                                ->afterStateHydrated(function ($component, $state, Get $get, ?Vendor $record) {
                                                    $existing = (int) ($record?->harga_vendor ?? 0);
                                                    if ($existing > 0) {
                                                        $component->state((string) $existing);
                                                        return;
                                                    }
                                                    $active = $record?->activePrice();
                                                    if ($active && (int) ($active->harga_vendor ?? 0) > 0) {
                                                        $component->state((string) (int) $active->harga_vendor);
                                                    }
                                                })
                                                ,

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
                                        ->reactive()
                                        ->afterStateHydrated(function ($component, $state, Get $get, Set $set, ?Vendor $record) {
                                            $items = collect($state ?? []);
                                            $active = $items->filter(fn ($item) => ($item['status'] ?? 'active') === 'active')
                                                ->sortByDesc(fn ($item) => $item['effective_from'] ?? now())
                                                ->first();
                                            if ($active) {
                                                $hp = (int) ($active['harga_publish'] ?? 0);
                                                $hv = (int) ($active['harga_vendor'] ?? 0);
                                                $profit = $hp - $hv;
                                                $set('harga_publish', $hp);
                                                $set('harga_vendor', $hv);
                                                $set('profit_amount', $profit);
                                                $set('profit_margin', $hp > 0 ? round(($profit / $hp) * 100, 2) : 0);
                                            }
                                        })
                                        ->afterStateUpdated(function ($state, Get $get, Set $set, ?Vendor $record) {
                                            $items = collect($state ?? []);
                                            $activeCount = $items->filter(fn ($item) => ($item['status'] ?? 'archived') === 'active')->count();
                                            if ($activeCount > 1) {
                                                Filament::notify('danger', 'Hanya satu riwayat harga dapat berstatus active. Ubah item active lain terlebih dahulu.');
                                                \Filament\Notifications\Notification::make()
                                                    ->danger()
                                                    ->title('Status aktif duplikat')
                                                    ->body('Hanya satu riwayat harga dapat berstatus active. Ubah item active lain terlebih dahulu.')
                                                    ->persistent()
                                                    ->send();
                                            }
                                            $active = $items->filter(fn ($item) => ($item['status'] ?? 'active') === 'active')
                                                ->sortByDesc(fn ($item) => $item['effective_from'] ?? now())
                                                ->first();
                                            if ($active) {
                                                $hp = (int) ($active['harga_publish'] ?? 0);
                                                $hv = (int) ($active['harga_vendor'] ?? 0);
                                                $profit = $hp - $hv;
                                                $set('harga_publish', $hp);
                                                $set('harga_vendor', $hv);
                                                $set('profit_amount', $profit);
                                                $set('profit_margin', $hp > 0 ? round(($profit / $hp) * 100, 2) : 0);
                                            }
                                        })
                                        ->schema([
                                            DateTimePicker::make('effective_from')
                                                ->label('Effective From')
                                                ->required(),
                                            DateTimePicker::make('effective_to')
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
                                                ->hidden(fn () => ! DbSchema::hasColumn('vendor_price_histories', 'status'))
                                                ->dehydrated(fn () => DbSchema::hasColumn('vendor_price_histories', 'status'))
                                                ->disableOptionWhen(function ($value, Get $get) {
                                                    if ($value !== 'active') {
                                                        return false;
                                                    }
                                                    $items = collect($get('priceHistories') ?? []);
                                                    $activeCount = $items->filter(fn ($item) => ($item['status'] ?? null) === 'active')->count();
                                                    $currentStatus = $get('status');
                                                    return $activeCount > 0 && $currentStatus !== 'active';
                                                })
                                                ->afterStateUpdated(function ($state, $component, Get $get) {
                                                    if ($state === 'active') {
                                                        $items = collect($get('priceHistories') ?? []);
                                                        $path = (string) $component->getStatePath();
                                                        $parts = explode('.', $path);
                                                        $idx = isset($parts[1]) ? (int) $parts[1] : -1;
                                                        $otherActive = $items->filter(function ($item, $key) {
                                                            return ($item['status'] ?? null) === 'active';
                                                        })->keys()->filter(fn ($key) => $key !== $idx)->count();
                                                        $newActiveCount = $otherActive + 1; // current becoming active
                                                        if ($newActiveCount > 1) {
                                                            Notification::make()
                                                                ->warning()
                                                                ->title('Status aktif duplikat')
                                                                ->body('Hanya satu riwayat harga dapat berstatus active. Ubah item active lain terlebih dahulu.')
                                                                ->persistent()
                                                                ->send();
                                                        }
                                                    }
                                                })
                                                ->helperText('Hanya satu item dapat berstatus active. Jika sudah ada active, opsi ini dinonaktifkan. Ubah item active menjadi archived atau scheduled terlebih dahulu.'),
                                                
                                            TextInput::make('harga_publish')
                                                ->label('Published Price')
                                                ->numeric()
                                                ->required()
                                                ->prefix('Rp')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',')
                                                ->numeric()
                                                ->rules(['integer', 'min:0'])
                                                ->live(onBlur: true)
                                                ->reactive()
                                                ->default(0),

                                            TextInput::make('harga_vendor')
                                                ->label('Vendor Price')
                                                ->numeric()
                                                ->required()
                                                ->default(0)
                                                ->prefix('Rp')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',')
                                                ->live(onBlur: true)
                                                ->reactive()
                                                ->rules(['min:0']),

                                            TextInput::make('profit_amount')
                                                ->label('Profit Amount History')
                                                ->prefix('Rp ')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(','),

                                            TextInput::make('profit_margin')
                                                ->label('Profit Margin')
                                                ->suffix('%')
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0), 2)),
                                        ])
                                        ->addActionLabel('Tambah Riwayat Harga')
                                        ->itemLabel(fn (array $state) => 'Harga mulai ' . ($state['effective_from'] ?? '-')),
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
                                        ->maxSize(10240)
                                        ->downloadable()
                                        ->openable()
                                        ->helperText('Upload PDF file (max 10MB)')
                                        ->columnSpanFull(),
                                ])
                                ->collapsible(),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    public static function build(Schema $schema): Schema
    {
        return $schema->components(self::components());
    }
}
