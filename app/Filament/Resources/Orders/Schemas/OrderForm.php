<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Resources\Orders\OrderResource;
use App\Enums\OrderStatus;
use App\Models\Expense;
use App\Models\NotaDinas;
use App\Models\NotaDinasDetail;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('Informasi Proyek')
                    ->icon('heroicon-o-information-circle')
                    ->description('Detail dasar proyek')
                    ->schema([
                        TextInput::make('number')
                            ->default('MW-'.random_int(100000, 999999))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(32)
                            ->unique(Order::class, 'number', ignoreRecord: true),
                        Select::make('prospect_id')
                            ->options(function (Get $get) {
                                $currentId = $get('prospect_id');
                                $query = Prospect::query()->whereDoesntHave('orders', function ($q) {
                                    $q->whereNotNull('status');
                                });
                                if ($currentId) {
                                    $query->orWhere('id', $currentId);
                                }
                                return $query->pluck('name_event', 'id')->toArray();
                            })
                            ->preload()
                            ->searchable()
                            ->required()
                            ->unique(Order::class, 'prospect_id', ignoreRecord: true)
                            ->label('Prospek')
                            ->debounce(500)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $prospect = Prospect::find($state);
                                    if ($prospect) {
                                        $set('name', $prospect->name_event);
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
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        TextInput::make('name')
                            ->required()
                            ->readOnly()
                            ->label('Nama Acara')
                            ->debounce(500),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->default(Auth::user()->id)
                            ->label('Account Manager'),
                        TextInput::make('slug')
                            ->readOnly()->maxLength(255),
                        Select::make('employee_id')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->required()
                            ->label('Event Manager')
                            ->helperText('Jika belum ada isi dengan makna wedding'),
                        TextInput::make('no_kontrak')
                            ->required()
                            ->label('No. Kontrak')
                            ->maxLength(255),
                        TextInput::make('pax')
                            ->required()
                            ->label('Pax')
                            ->default(1000)
                            ->numeric(),
                        FileUpload::make('doc_kontrak')
                            ->label('Upload Kontrak')
                            ->reorderable()
                            ->required()
                            ->helperText('pastikan kontrak sudah semua ditanda tangani')
                            ->openable()
                            ->directory('doc_kontrak')
                            ->downloadable()
                            ->acceptedFileTypes(['application/pdf']),
                        RichEditor::make('note')
                            ->label('Keterangan Tambahan')
                            ->fileAttachmentsDirectory('orders')
                            ->columnSpan(2)
                            ->fileAttachmentsDisk('public'),
                        ToggleButtons::make('status')
                            ->inline()
                            ->options(OrderStatus::class)
                            ->label('Status Pesanan')
                            ->required()
                            ->helperText('Status Done: Finance hanya bisa view, Super Admin bisa edit.'),
                    ]),

                Step::make('Detail Pembayaran')
                    ->icon('heroicon-o-currency-dollar')
                    ->description('Produk dan informasi pembayaran')
                    ->schema([
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
                                                ->required()
                                                ->placeholder('1, 2, 3 dst'),
                                            Select::make('payment_method_id')
                                                ->relationship('paymentMethod', 'name')
                                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->is_cash ? 'Kas/Tunai' : ($record->bank_name ? "{$record->bank_name} - {$record->no_rekening}" : $record->name))
                                                ->required()
                                                ->label('Metode Pembayaran'),
                                            TextInput::make('nominal')
                                                ->numeric()
                                                ->prefix('Rp. ')
                                                ->label('Nominal')
                                                ->required()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',')
                                                ->debounce(800)
                                                ->lazy()
                                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                    if ($state !== null) {
                                                        OrderResource::updateDependentFinancialFields($get, $set);
                                                    }
                                                }),
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
                                                ->label('Tgl. Bayar')
                                                ->live(onBlur: true),
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
                                                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                                ->helperText('Max 1MB. JPG or PNG only.'),
                                        ]),
                                    ])
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        OrderResource::updateDependentFinancialFields($get, $set);
                                    })
                                    ->collapsible()
                                    ->reorderable()
                                    ->cloneable()
                                    ->live(onBlur: true)
                                    ->itemLabel(
                                        fn (array $state): ?string => $state['keterangan'] ?? 'New Payment',
                                    ),
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
                            ->helperText('Auto-calculated from selected products penambahan publish price')
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
                            ->label('Total Pengurangan dari Produk (Otomatis)')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->dehydrated()
                            ->readOnly()
                            ->helperText('Nilai ini dihitung otomatis dari total pengurangan semua produk dalam order.'),
                    ]),

                Step::make('Informasi Keuangan')
                    ->icon('heroicon-o-banknotes')
                    ->description('Catat detail keuangan')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('bayar')
                                    ->label('Uang dibayar')
                                    ->readOnly()
                                    ->helperText('Pembayaran klien ke rek makna')
                                    ->default(0)
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(','),

                                TextInput::make('grand_total')
                                    ->label('Grand Total')
                                    ->readOnly()
                                    ->helperText('Grand Total = Total Paket + Penambahan - Promo - Pengurangan')
                                    ->default(0)
                                    ->numeric()
                                    ->dehydrated(true)
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(','),

                                TextInput::make('tot_pengeluaran')
                                    ->label('Pengeluaran')
                                    ->readOnly()
                                    ->numeric()
                                    ->helperText('Total Pembayaran Ke Vendor')
                                    ->reactive()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->dehydrated(true)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->tot_pengeluaran);
                                        }
                                    }),

                                TextInput::make('sisa')
                                    ->label('Sisa Pembayaran')
                                    ->readOnly()
                                    ->default(0)
                                    ->helperText('Sisa uang yang harus di bayar ke makna')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->dehydrated(true)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->sisa);
                                        }
                                    }),

                                TextInput::make('laba_kotor')
                                    ->label('Laba Kotor')
                                    ->readOnly()
                                    ->numeric()
                                    ->helperText('Grand total - Pembayaran ke vendor')
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->dehydrated(true)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->laba_kotor);
                                        }
                                    }),
                                TextInput::make('uang_diterima')
                                    ->label('Uang Diterima')
                                    ->readOnly()
                                    ->default(0)
                                    ->helperText('Sisa uang yang diterima dari klien')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->dehydrated(true)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->uang_diterima);
                                        }
                                    }),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),

                        DatePicker::make('closing_date')
                            ->date()
                            ->label('Closing Date (Otomatis dari Pembayaran Pertama)')
                            ->readOnly()
                            ->default(function (Get $get, ?Order $record): string {
                                if ($record && $record->exists) {
                                    $firstPayment = $record->dataPembayaran()->orderBy('tgl_bayar', 'asc')->first();
                                    if ($firstPayment && $firstPayment->tgl_bayar) {
                                        return Carbon::parse($firstPayment->tgl_bayar)->format('Y-m-d');
                                    }
                                }
                                $paymentItems = $get('Jika Ada Pembayaran') ?? [];
                                if (! empty($paymentItems)) {
                                    usort($paymentItems, function ($a, $b) {
                                        return strtotime($a['tgl_bayar'] ?? 'now') <=> strtotime($b['tgl_bayar'] ?? 'now');
                                    });
                                    if (isset($paymentItems[0]['tgl_bayar']) && ! empty($paymentItems[0]['tgl_bayar'])) {
                                        return Carbon::parse($paymentItems[0]['tgl_bayar'])->format('Y-m-d');
                                    }
                                }
                                return now()->format('Y-m-d');
                            })->columnSpanFull(),

                        Toggle::make('is_paid')
                            ->label('Lunas / Belum')
                            ->default(false)
                            ->disabled()
                            ->reactive()
                            ->live()
                            ->dehydrated()
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user')
                            ->helperText('Otomatis lunas jika sisa pembayaran > 0'),
                    ]),

                Step::make('Pengeluaran')
                    ->icon('heroicon-o-book-open')
                    ->description('Catat detail pengeluaran')
                    ->schema([
                        Section::make('Pengeluaran')
                            ->description('Catat pengeluaran ke vendor. Setiap vendor hanya boleh dipilih satu kali per order.')
                            ->schema([
                                Repeater::make('expenses')
                                    ->relationship('expenses')
                                    ->live()
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('nota_dinas_id')
                                                    ->label('Nota Dinas')
                                                    ->options(function (callable $get) {
                                                        $orderId = $get('../../id');
                                                        if (! $orderId) {
                                                            return [];
                                                        }
                                                        return NotaDinas::whereHas('details', function ($query) use ($orderId) {
                                                            $query->where('order_id', $orderId);
                                                        })->pluck('no_nd', 'id')->toArray();
                                                    })
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        if (! $state) {
                                                            $set('vendor_id', null);
                                                            $set('note', null);
                                                            $set('amount', null);
                                                            $set('account_holder', null);
                                                            $set('bank_name', null);
                                                            $set('bank_account', null);
                                                            $set('no_nd', null);
                                                        } else {
                                                            $notaDinas = NotaDinas::find($state);
                                                            if ($notaDinas) {
                                                                $set('no_nd', $notaDinas->no_nd);
                                                            }
                                                        }
                                                    }),

                                                Hidden::make('no_nd')
                                                    ->dehydrated()
                                                    ->label('No. Nota Dinas'),

                                                Select::make('nota_dinas_detail_id')
                                                    ->label('Detail Nota Dinas')
                                                    ->options(function (callable $get) {
                                                        $notaDinasId = $get('nota_dinas_id');
                                                        if (! $notaDinasId) {
                                                            return [];
                                                        }
                                                        try {
                                                            $currentExpenseItems = $get('../../expenses') ?? $get('../expenses') ?? $get('expenses') ?? [];
                                                            $currentDetailId = $get('nota_dinas_detail_id');
                                                            $currentExpenseId = $get('id');
                                                            $orderId = $get('../../id') ?? $get('../id') ?? $get('id');
                                                            $usedDetailIds = [];
                                                            foreach ($currentExpenseItems as $item) {
                                                                if (isset($item['nota_dinas_detail_id']) &&
                                                                    $item['nota_dinas_detail_id'] !== $currentDetailId &&
                                                                    (! isset($item['id']) || $item['id'] !== $currentExpenseId)) {
                                                                    $usedDetailIds[] = $item['nota_dinas_detail_id'];
                                                                }
                                                            }
                                                            if ($orderId) {
                                                                $dbUsedIds = Expense::where('order_id', $orderId)
                                                                    ->whereNotNull('nota_dinas_detail_id')
                                                                    ->when($currentExpenseId, function ($query) use ($currentExpenseId) {
                                                                        return $query->where('id', '!=', $currentExpenseId);
                                                                    })
                                                                    ->pluck('nota_dinas_detail_id')
                                                                    ->toArray();
                                                                $usedDetailIds = array_unique(array_merge($usedDetailIds, $dbUsedIds));
                                                            }
                                                            $availableDetails = NotaDinasDetail::with('vendor')
                                                                ->where('nota_dinas_id', $notaDinasId)
                                                                ->where('jenis_pengeluaran', 'wedding')
                                                                ->whereNotIn('id', $usedDetailIds)
                                                                ->whereHas('vendor')
                                                                ->get();
                                                            if ($currentDetailId && ! $availableDetails->contains('id', $currentDetailId)) {
                                                                $currentDetail = NotaDinasDetail::with('vendor')
                                                                    ->where('jenis_pengeluaran', 'wedding')
                                                                    ->find($currentDetailId);
                                                                if ($currentDetail && $currentDetail->vendor) {
                                                                    $availableDetails->prepend($currentDetail);
                                                                }
                                                            }
                                                            return $availableDetails->mapWithKeys(function ($detail) use ($usedDetailIds) {
                                                                $vendorName = $detail->vendor->name ?? 'N/A';
                                                                $keperluan = $detail->keperluan ?? 'N/A';
                                                                $paymentStage = $detail->payment_stage ? " | {$detail->payment_stage}" : '';
                                                                $jumlah = number_format($detail->jumlah_transfer, 0, ',', '.');
                                                                $usedIndicator = in_array($detail->id, $usedDetailIds) ? ' (Tersedia kembali)' : '';
                                                                $label = "{$vendorName} | {$keperluan}{$paymentStage} | Rp {$jumlah}{$usedIndicator}";
                                                                return [$detail->id => $label];
                                                            })->toArray();
                                                        } catch (Exception $e) {
                                                            Log::error('Error in nota_dinas_detail_id options: '.$e->getMessage(), [
                                                                'nota_dinas_id' => $notaDinasId,
                                                                'trace' => $e->getTraceAsString(),
                                                            ]);
                                                            return [];
                                                        }
                                                    })
                                                    ->searchable()
                                                    ->reactive()
                                                    ->live()
                                                    ->helperText(function (callable $get) {
                                                        try {
                                                            $notaDinasId = $get('nota_dinas_id');
                                                            if (! $notaDinasId) {
                                                                return 'Pilih Nota Dinas terlebih dahulu';
                                                            }
                                                            $currentExpenseItems = $get('../../expenses') ?? $get('../expenses') ?? $get('expenses') ?? [];
                                                            $orderId = $get('../../id') ?? $get('../id') ?? $get('id');
                                                            $formUsedIds = array_filter(array_column($currentExpenseItems, 'nota_dinas_detail_id'));
                                                            $dbUsedIds = $orderId ? Expense::where('order_id', $orderId)
                                                                ->whereNotNull('nota_dinas_detail_id')
                                                                ->pluck('nota_dinas_detail_id')
                                                                ->toArray() : [];
                                                            $allUsedIds = array_unique(array_merge($formUsedIds, $dbUsedIds));
                                                            $actualUsedCount = count($allUsedIds);
                                                            $totalCount = NotaDinasDetail::where('nota_dinas_id', $notaDinasId)
                                                                ->where('jenis_pengeluaran', 'wedding')
                                                                ->count();
                                                            return "Pilih detail nota dinas yang akan dibayar (Sudah dipilih: {$actualUsedCount}/{$totalCount})";
                                                        } catch (Exception $e) {
                                                            Log::warning('Error in helperText: '.$e->getMessage());
                                                            return 'Pilih detail nota dinas yang akan dibayar';
                                                        }
                                                    })
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        try {
                                                            if (! $state) {
                                                                $set('vendor_id', null);
                                                                $set('account_holder', null);
                                                                $set('bank_name', null);
                                                                $set('bank_account', null);
                                                                $set('amount', null);
                                                                $set('note', null);
                                                                return;
                                                            }
                                                            $notaDinasDetail = NotaDinasDetail::with('vendor')->find($state);
                                                            if ($notaDinasDetail) {
                                                                $set('vendor_id', $notaDinasDetail->vendor_id);
                                                                $set('account_holder', $notaDinasDetail->account_holder ?? $notaDinasDetail->vendor->account_holder);
                                                                $set('bank_name', $notaDinasDetail->bank_name ?? $notaDinasDetail->vendor->bank_name);
                                                                $set('bank_account', $notaDinasDetail->bank_account ?? $notaDinasDetail->vendor->bank_account);
                                                                $set('amount', OrderResource::safeFloatVal($notaDinasDetail->jumlah_transfer ?? 0));
                                                                $set('note', $notaDinasDetail->keperluan ?? null);
                                                            }
                                                        } catch (Exception $e) {
                                                            Log::error('Error in afterStateUpdated: '.$e->getMessage());
                                                        }
                                                    })
                                                    ->required()
                                                    ->columnSpan(2),

                                                Hidden::make('vendor_id'),

                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('bank_name')
                                                    ->label('Bank')
                                                    ->required()
                                                    ->live()
                                                    ->columnSpan(1),

                                                TextInput::make('account_holder')
                                                    ->label('Nama Rekening')
                                                    ->required()
                                                    ->live()
                                                    ->columnSpan(1),

                                                TextInput::make('bank_account')
                                                    ->label('Nomor Rekening')
                                                    ->required()
                                                    ->live()
                                                    ->columnSpan(1),
                                            ]),

                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('amount')
                                                    ->label('Jumlah Transfer')
                                                    ->numeric()
                                                    ->prefix('Rp. ')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->dehydrateStateUsing(fn ($state) => floatval(str_replace([',', '.'], ['', '.'], $state ?? 0)))
                                                    ->required(),

                                                Select::make('payment_method_id')
                                                    ->label('Metode Pembayaran')
                                                    ->required()
                                                    ->options(PaymentMethod::all()
                                                        ->pluck('name', 'id')),

                                                DatePicker::make('date_expense')
                                                    ->label('Tanggal Pengeluaran')
                                                    ->default(now())
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        try {
                                                            $vendorId = $get('vendor_id');
                                                            if (! $vendorId) {
                                                                return;
                                                            }
                                                            $vendor = Vendor::find($vendorId);
                                                            if (! $vendor) {
                                                                return;
                                                            }
                                                            $at = $state ? Carbon::parse($state) : now();
                                                            $active = $vendor->activePrice($at);
                                                            $currentAmount = OrderResource::safeFloatVal($get('amount'));
                                                            if (($currentAmount ?? 0) <= 0 && $active) {
                                                                $set('amount', $active->harga_vendor);
                                                            }
                                                        } catch (\Throwable $e) {
                                                        }
                                                    }),
                                            ]),

                                        Grid::make(1)
                                            ->schema([
                                                Textarea::make('note')
                                                    ->label('Catatan / Keperluan')
                                                    ->required()
                                                    ->rows(3)
                                                    ->columnSpan(1),
                                                FileUpload::make('image')
                                                    ->label('Bukti Transfer')
                                                    ->directory('expense-proofs')
                                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                                    ->maxSize(5120)
                                                    ->required()
                                                    ->downloadable()
                                                    ->openable()
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->collapsed(false)
                                    ->itemLabel(function (array $state): ?string {
                                        if (! isset($state['vendor_id']) || ! $state['vendor_id']) {
                                            return '🆕 Expense Baru';
                                        }
                                        try {
                                            $vendor = Vendor::find($state['vendor_id']);
                                            $vendorName = $vendor?->name ?? 'Vendor #'.$state['vendor_id'];
                                            $formatCurrency = function ($value) {
                                                if (empty($value)) {
                                                    return 'Rp 0';
                                                }
                                                if (is_string($value)) {
                                                    $value = preg_replace('/[^\d.,]/', '', $value);
                                                    $value = str_replace(',', '', $value);
                                                }
                                                $numericValue = OrderResource::safeFloatVal($value);
                                                return 'Rp '.number_format($numericValue, 0, ',', '.');
                                            };
                                            $formattedAmount = $formatCurrency($state['amount'] ?? 0);
                                            $paymentStage = 'DP';
                                            if (isset($state['nota_dinas_detail_id'])) {
                                                try {
                                                    $notaDinasDetail = NotaDinasDetail::find($state['nota_dinas_detail_id']);
                                                    $paymentStage = $notaDinasDetail?->payment_stage ?? 'DP';
                                                } catch (Exception $e) {
                                                }
                                            }
                                            return "🏪 {$vendorName} ({$paymentStage}) - {$formattedAmount}";
                                        } catch (Exception $e) {
                                            Log::warning('Error in expense itemLabel: '.$e->getMessage());
                                            return '⚠️ Expense Item';
                                        }
                                    })
                                    ->addActionLabel('Tambah Expense')
                                    ->reorderable()
                                    ->cloneable()
                                    ->afterStateUpdated(function ($state, $livewire) {
                                        $livewire->dispatch('refreshForm');
                                    }),
                            ])->columnSpanFull(),
                    ]),

                Step::make('Riwayat Modifikasi')
                    ->icon('heroicon-o-clock')
                    ->description('Catat detail modifikasi')
                    ->schema([
                        TextInput::make('created_at_display')
                            ->label('Dibuat')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, ?Order $record): void {
                                $component->state($record?->created_at?->diffForHumans());
                            }),
                        TextInput::make('updated_at_display')
                            ->label('Terakhir Diubah')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, ?Order $record): void {
                                $component->state($record?->updated_at?->diffForHumans());
                            }),
                        TextInput::make('last_edited_by_display')
                            ->label('Terakhir Diedit Oleh')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, ?Order $record): void {
                                if ($record?->lastEditedBy) {
                                    $component->state($record->lastEditedBy->name.' pada '.$record->updated_at?->format('d M Y H:i'));
                                } else {
                                    $component->state('Belum dilacak');
                                }
                            }),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Order $record) => $record === null),
            ])
                ->columnSpan('full')
                ->columns(3)
                ->skippable(),
        ]);
    }
}
