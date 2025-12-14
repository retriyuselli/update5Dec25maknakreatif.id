<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product dipesan')
                ->schema([OrderResource::getItemsRepeater()])
                ->columnSpanFull(),

            Section::make('Data Pembayaran')
                ->schema([
                    Repeater::make('Jika Ada Pembayaran')
                        ->relationship('dataPembayaran')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('keterangan')
                                    ->label('Keterangan')
                                    ->prefix('Pembayaran')
                                    ->required(),

                                Select::make('payment_method_id')
                                    ->relationship('paymentMethod', 'name')
                                    ->required()
                                    ->label('Metode Pembayaran'),

                                TextInput::make('nominal')
                                    ->numeric()
                                    ->prefix('Rp. ')
                                    ->label('Nominal')
                                    ->required()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(','),

                                Select::make('kategori_transaksi')
                                    ->options([
                                        'uang_masuk' => 'Uang Masuk',
                                        'uang_keluar' => 'Uang Keluar',
                                    ])
                                    ->default('uang_masuk')
                                    ->label('Tipe Transaksi')
                                    ->required(),

                                DatePicker::make('tgl_bayar')
                                    ->date()
                                    ->required()
                                    ->label('Tgl. Bayar'),

                                FileUpload::make('image')
                                    ->label('Payment Proof')
                                    ->image()
                                    ->required()
                                    ->maxSize(1280)
                                    ->disk('public')
                                    ->directory('payment-proofs/'.date('Y/m'))
                                    ->visibility('public')
                                    ->downloadable()
                                    ->openable()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png']),
                            ]),
                        ])
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            OrderResource::updateDependentFinancialFields($get, $set);
                        })
                        ->collapsible()
                        ->reorderable()
                        ->cloneable()
                        ->live(onBlur: true)
                        ->itemLabel(fn (array $state): ?string => $state['keterangan'] ?? 'New Payment'),
                ])
                ->columnSpanFull(),

            TextInput::make('total_price')
                ->numeric()
                ->prefix('Rp. ')
                ->label('Total Paket Awal')
                ->readOnly()
                ->default(0)
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(','),

            Hidden::make('is_cash')
                ->dehydrated(false),

            TextInput::make('promo')
                ->default(0)
                ->numeric()
                ->prefix('Rp. ')
                ->readOnly()
                ->label('Promo')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->reactive()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $total_price = OrderResource::safeFloatVal($get('total_price'));
                    $pengurangan_val = OrderResource::safeFloatVal($get('pengurangan'));
                    $promo_val = OrderResource::safeFloatVal($get('promo'));
                    $penambahan_val = OrderResource::safeFloatVal($get('penambahan'));
                    $grandTotal = $total_price + $penambahan_val - $promo_val - $pengurangan_val;
                    $set('grand_total', $grandTotal);
                    OrderResource::updateDependentFinancialFields($get, $set);
                }),

            TextInput::make('penambahan')
                ->default(0)
                ->numeric()
                ->prefix('Rp. ')
                ->readOnly()
                ->label('Penambahan Harga')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->reactive()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $total_price = OrderResource::safeFloatVal($get('total_price'));
                    $pengurangan_val = OrderResource::safeFloatVal($get('pengurangan'));
                    $promo_val = OrderResource::safeFloatVal($get('promo'));
                    $penambahan_val = OrderResource::safeFloatVal($get('penambahan'));
                    $grandTotal = $total_price + $penambahan_val - $promo_val - $pengurangan_val;
                    $set('grand_total', $grandTotal);
                    OrderResource::updateDependentFinancialFields($get, $set);
                }),

            TextInput::make('pengurangan')
                ->default(0)
                ->numeric()
                ->prefix('Rp. ')
                ->readOnly()
                ->label('Pengurangan Harga')
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->reactive()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $total_price = OrderResource::safeFloatVal($get('total_price'));
                    $pengurangan_val = OrderResource::safeFloatVal($get('pengurangan'));
                    $promo_val = OrderResource::safeFloatVal($get('promo'));
                    $penambahan_val = OrderResource::safeFloatVal($get('penambahan'));
                    $grandTotal = $total_price + $penambahan_val - $promo_val - $pengurangan_val;
                    $set('grand_total', $grandTotal);
                    OrderResource::updateDependentFinancialFields($get, $set);
                }),

            TextInput::make('grand_total')
                ->numeric()
                ->prefix('Rp. ')
                ->label('Grand Total')
                ->readOnly()
                ->default(0)
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(','),
        ]);
    }
}
