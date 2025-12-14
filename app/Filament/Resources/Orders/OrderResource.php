<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\Invoice;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Widgets\OrderOverview;
use App\Filament\Resources\Orders\Widgets\UangDiterimaOverview;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrderTable;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Proyek Wedding';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-shopping-cart';

    protected static ?int $navigationSort = 1;

    /**
     * Safely convert any value to float for calculations
     */
    public static function safeFloatVal($value): float
    {
        if (is_null($value)) {
            return 0.0;
        }

        if (is_numeric($value)) {
            return floatval($value);
        }

        if (is_string($value)) {
            // Remove any non-numeric characters except dots and commas
            $cleaned = preg_replace('/[^\d.,]/', '', $value);
            // Remove commas (thousand separators)
            $cleaned = str_replace(',', '', $cleaned);
            // Handle empty string after cleaning
            if ($cleaned === '' || $cleaned === '.') {
                return 0.0;
            }

            return floatval($cleaned);
        }

        if (is_array($value)) {
            // If somehow we get an array, return 0
            Log::warning('Received array value in safeFloatVal', ['value' => $value]);

            return 0.0;
        }

        // Fallback for any other data type
        Log::warning('Unexpected data type in safeFloatVal', [
            'value' => $value,
            'type' => gettype($value),
        ]);

        return 0.0;
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [OrderOverview::class, UangDiterimaOverview::class];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', 'processing')->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'prospect.name_event', 'user.name', 'employee.name', 'user.name'];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
            'invoice' => Invoice::route('/{record}/invoice'),
        ];
    }

    /**
     * Override the base query to include soft-deleted records.
     * This allows the TrashedFilter to work correctly.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);

        // Super admin and finance can access all orders
        if (Auth::check()) {
            if (Auth::user()?->hasRole('super_admin') || Auth::user()?->hasRole('Finance') || Auth::user()?->hasRole('admin_am')) {
                return $query;
            }
        }

        // Other users can only access their own orders (as Account Manager)
        return $query->where('user_id', Auth::user()->id);
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->schema([
                Select::make('product_id')
                    ->label('Product')
                    ->options(Product::query()->where('stock', '>', 1)->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->live() // Anda bisa menambahkan live() jika ingin update instan saat produk dipilih
                    ->afterStateHydrated(function (Set $set, Get $get, $state) {
                        $product = Product::find($state);
                        $set('unit_price', $product?->product_price ?? 0);
                        $set('stock', $product?->stock ?? 0);
                    })

                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $product = Product::find($state);
                        $set('unit_price', $product?->product_price ?? 0);
                        $set('stock', $product?->stock ?? 0);
                        $quantity = $get('quantity') ?? 1; // Get quantity or default to 1
                        $stock = $get('stock');
                        self::updateTotalPrice($get, $set);
                    })
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan([
                        'md' => 5,
                    ])
                    ->searchable(),
                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->columnSpan([
                        'md' => 1,
                    ])
                    ->minValue(1)
                    ->required()
                    ->reactive()
                    // ->live() // Anda bisa menambahkan live() jika ingin update instan saat kuantitas diubah
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $stock = $get('stock');
                        if ($state > $stock) {
                            $set('quantity', $stock);
                            Notification::make()->title('Stock tidak mencukupi')->warning()->send();
                        }
                        self::updateTotalPrice($get, $set);
                    }),
                TextInput::make('stock')
                    ->label('Stok')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required()
                    ->columnSpan([
                        'md' => 1,
                    ]),
                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->disabled()
                    ->dehydrated()
                    ->prefix('Rp. ')
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->required()
                    ->columnSpan([
                        'md' => 3,
                    ]),
            ])
            ->collapsible()
            ->reorderable()
            ->cloneable()
            ->reactive()
            ->live()
            ->itemLabel(fn (array $state): ?string => Product::find($state['product_id'])?->name)
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);
                        $product = Product::find($itemData['product_id']);
                        if (! $product) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['product_id'])),
            ])
            ->defaultItems(1)
            ->hiddenLabel()
            ->columns([
                'md' => 10,
            ])
            ->reactive() // Membuat repeater reaktif
            // ->live() // Anda bisa menambahkan live() jika ingin update instan saat item ditambah/dihapus
            ->afterStateUpdated(function (Get $get, Set $set) {
                // Logika ini akan dijalankan ketika item di repeater berubah (ditambah, dihapus, atau field reaktif di dalamnya berubah)
                // $get relatif terhadap parent dari repeater (dalam kasus ini, Wizard\Step 'Payment Details')
                $orderItems = $get('items') ?? []; // 'items' adalah nama repeater
                $calculatedProductPengurangan = 0;
                $calculatedProductPenambahan = 0;
                $calculatedTotalPrice = 0;

                if (is_array($orderItems)) {
                    foreach ($orderItems as $item) {
                        if (! empty($item['product_id']) && ! empty($item['quantity'])) {
                            $product = Product::find($item['product_id']);
                            if ($product) {
                                // Akumulasi total pengurangan dari produk (kuantitas * pengurangan produk)
                                $calculatedProductPengurangan += $item['quantity'] * ($product->pengurangan ?? 0);
                                // Akumulasi total penambahan dari produk (kuantitas * penambahan_publish produk)
                                $calculatedProductPenambahan += $item['quantity'] * ($product->penambahan_publish ?? 0);
                                // Akumulasi total harga berdasarkan harga jual produk (kuantitas * harga produk)
                                $calculatedTotalPrice += $item['quantity'] * ($product->product_price ?? 0);
                            }
                        }
                    }
                }

                $set('pengurangan', $calculatedProductPengurangan); // Mengatur field 'pengurangan' di form Order
                $set('penambahan', $calculatedProductPenambahan); // Mengatur field 'penambahan' dari penambahan_publish produk
                $set('total_price', $calculatedTotalPrice); // Mengatur field 'total_price' di form Order

                // Hitung ulang grand_total berdasarkan nilai baru
                $promo = self::safeFloatVal($get('promo'));
                $grandTotal = $calculatedTotalPrice + $calculatedProductPenambahan - $promo - $calculatedProductPengurangan;
                $set('grand_total', $grandTotal); // Mengatur field 'grand_total' di form Order
            });
    }

    public static function updateTotalPrice(Get $get, Set $set): void
    {
        $selectedProducts = collect($get('items'))->filter(fn ($item) => ! empty($item['product_id']) && ! empty($item['quantity']));

        $productIds = $selectedProducts->pluck('product_id')->unique()->filter()->toArray();

        // Fetch products from DB and key by ID for efficient lookup
        $productsFromDb = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $calculatedTotalPrice = 0;
        $calculatedProductPengurangan = 0;
        $calculatedProductPenambahan = 0;

        foreach ($selectedProducts as $item) {
            $productId = $item['product_id'];
            $quantity = self::safeFloatVal($item['quantity'] ?? 0);

            // Check if product exists in our fetched collection and has a price
            if (isset($productsFromDb[$productId]) && isset($productsFromDb[$productId]->price)) {
                $productPrice = self::safeFloatVal($productsFromDb[$productId]->product_price ?? 0);
                $productPengurangan = self::safeFloatVal($productsFromDb[$productId]->pengurangan ?? 0);
                $productPenambahanPublish = self::safeFloatVal($productsFromDb[$productId]->penambahan_publish ?? 0);

                $calculatedTotalPrice += $productPrice * $quantity;
                $calculatedProductPengurangan += $productPengurangan * $quantity;
                $calculatedProductPenambahan += $productPenambahanPublish * $quantity;
            }
        }

        $set('total_price', $calculatedTotalPrice);
        $set('pengurangan', $calculatedProductPengurangan); // Set field 'pengurangan'
        $set('penambahan', $calculatedProductPenambahan); // Set field 'penambahan' from product's penambahan_publish

        // Recalculate grand_total
        $promo = self::safeFloatVal($get('promo'));
        // Gunakan $calculatedProductPengurangan dan $calculatedProductPenambahan yang baru dihitung
        $grandTotal = $calculatedTotalPrice + $calculatedProductPenambahan - $promo - $calculatedProductPengurangan;
        $set('grand_total', $grandTotal);

        // Panggil method baru untuk update sisa dan is_paid
        self::updateDependentFinancialFields($get, $set);
    }

    public static function updateExchangePaid(Get $get, Set $set): void
    {
        $paidAmount = (int) $get('paid_amount') ?? 0;
        $totalPrice = (int) $get('total_price') ?? 0;
        $promoPrice = (int) $get('promo') ?? 0;
        $penambahanPrice = (int) $get('penambahan') ?? 0;
        $penguranganPrice = (int) $get('pengurangan') ?? 0;
        $exchangePaid = $totalPrice - $paidAmount - $promoPrice - $penguranganPrice + $penambahanPrice;
        $set('change_amount', $exchangePaid);
    }

    public static function updateDependentFinancialFields(Get $get, Set $set): void
    {
        // Pertama, pastikan grand_total dihitung ulang dengan safe conversion
        $total_price = self::safeFloatVal($get('total_price'));
        $pengurangan_val = self::safeFloatVal($get('pengurangan'));
        $promo_val = self::safeFloatVal($get('promo'));
        $penambahan_val = self::safeFloatVal($get('penambahan'));
        $grandTotal = $total_price + $penambahan_val - $promo_val - $pengurangan_val;
        $set('grand_total', $grandTotal);

        // Hitung 'bayar' dari repeater 'dataPembayaran' dengan safe conversion
        $paymentItems = $get('Jika Ada Pembayaran') ?? [];
        $bayar = 0;
        if (is_array($paymentItems)) {
            foreach ($paymentItems as $paymentItem) {
                $nominalValue = $paymentItem['nominal'] ?? 0;
                $bayar += self::safeFloatVal($nominalValue);
            }
        }
        $set('bayar', $bayar);

        // Hitung 'sisa'
        $sisa = $grandTotal - $bayar;
        $set('sisa', $sisa);

        // Update 'is_paid'
        $set('is_paid', $sisa <= 0);

        // Update 'closing_date' based on the first payment date
        self::updateClosingDate($get, $set);
    }

    protected static function updateClosingDate(Get $get, Set $set): void
    {
        $paymentItems = $get('Jika Ada Pembayaran') ?? [];
        if (! empty($paymentItems)) {
            // Urutkan pembayaran berdasarkan tgl_bayar untuk mendapatkan yang paling awal
            usort($paymentItems, function ($a, $b) {
                return strtotime($a['tgl_bayar'] ?? 'now') <=> strtotime($b['tgl_bayar'] ?? 'now');
            });
            if (isset($paymentItems[0]['tgl_bayar']) && ! empty($paymentItems[0]['tgl_bayar'])) {
                $set('closing_date', Carbon::parse($paymentItems[0]['tgl_bayar'])->format('Y-m-d'));

                return; // Keluar setelah menemukan tanggal pembayaran pertama
            }
        }
        // Jika tidak ada pembayaran, bisa di-set ke default atau dibiarkan (tergantung kebutuhan)
        // $set('closing_date', now()->format('Y-m-d')); // Atau biarkan saja jika tidak ada pembayaran
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total proyek yang sedang diproses';
    }
}
