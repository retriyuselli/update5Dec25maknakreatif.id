<?php

namespace App\Filament\Resources\PengeluaranLains;

use App\Filament\Resources\PengeluaranLains\Pages\CreatePengeluaranLain;
use App\Filament\Resources\PengeluaranLains\Pages\EditPengeluaranLain;
use App\Filament\Resources\PengeluaranLains\Pages\ListPengeluaranLains;
use App\Filament\Resources\PengeluaranLains\Widgets\PengeluaranOverviewWidgets;
use App\Models\NotaDinas;
use App\Models\NotaDinasDetail;
use App\Models\PengeluaranLain;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PengeluaranLainResource extends Resource
{
    protected static ?string $model = PengeluaranLain::class;

    protected static ?string $navigationLabel = 'Pengeluaran Lain';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    /**
     * Safely convert any value to float for calculations
     */
    private static function safeFloatVal($value): float
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
            return 0.0;
        }

        // Fallback for any other data type
        return 0.0;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pengeluaran Lain')
                ->description('Detail pengeluaran di luar operasional harian')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Pengeluaran')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->placeholder('Akan terisi otomatis dari detail nota dinas atau isi manual')
                            ->helperText('Terisi otomatis dari "Keperluan + Event" pada detail nota dinas yang dipilih')
                            ->columnSpan(1),
                        TextInput::make('amount')
                            ->required()
                            ->label('Nominal')
                            ->prefix('Rp. ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->inputMode('numeric')
                            ->placeholder('0')
                            ->columnSpan(1),
                    ]),
                ]),

            Section::make('Detail Transaksi & Nota Dinas')
                ->description('Informasi pembayaran melalui Nota Dinas dan dokumentasi')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('nota_dinas_id')
                                ->label('Nota Dinas')
                                ->options(function () {
                                    // Filter NotaDinas yang memiliki detail dengan jenis_pengeluaran 'lain-lain'
                                    return NotaDinas::whereIn('status', ['disetujui', 'diajukan'])
                                        ->whereHas('details', function ($query) {
                                            $query->where('jenis_pengeluaran', 'lain-lain');
                                        })
                                        ->orderBy('created_at', 'desc')
                                        ->pluck('no_nd', 'id')
                                        ->toArray();
                                })
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Reset related fields when nota dinas changes
                                    if (! $state) {
                                        $set('nota_dinas_detail_id', null);
                                        $set('vendor_id', null);
                                        $set('bank_name', null);
                                        $set('account_holder', null);
                                        $set('bank_account', null);
                                        $set('amount', null);
                                        $set('note', null);
                                        $set('name', null);
                                    }
                                })
                                ->columnSpan(1),

                            Select::make('nota_dinas_detail_id')
                                ->label('Detail Nota Dinas')
                                ->options(function (callable $get) {
                                    $notaDinasId = $get('nota_dinas_id');
                                    if (! $notaDinasId) {
                                        return [];
                                    }

                                    try {
                                        $currentDetailId = $get('nota_dinas_detail_id');

                                        // Get used detail IDs from other PengeluaranLain records
                                        $usedDetailIds = PengeluaranLain::whereNotNull('nota_dinas_detail_id')
                                            ->when($get('id'), function ($query) use ($get) {
                                                return $query->where('id', '!=', $get('id'));
                                            })
                                            ->pluck('nota_dinas_detail_id')
                                            ->toArray();

                                        // Single optimized query - Filter only 'lain-lain' jenis_pengeluaran
                                        $availableDetails = NotaDinasDetail::with('vendor')
                                            ->where('nota_dinas_id', $notaDinasId)
                                            ->where('jenis_pengeluaran', 'lain-lain') // Filter hanya untuk jenis pengeluaran 'lain-lain'
                                            ->whereNotIn('id', $usedDetailIds)
                                            ->whereHas('vendor')
                                            ->get();

                                        // Preserve current selection
                                        if ($currentDetailId && ! $availableDetails->contains('id', $currentDetailId)) {
                                            $currentDetail = NotaDinasDetail::with('vendor')->find($currentDetailId);
                                            if ($currentDetail && $currentDetail->vendor) {
                                                $availableDetails->prepend($currentDetail);
                                            }
                                        }

                                        return $availableDetails->mapWithKeys(function ($detail) use ($usedDetailIds) {
                                            $vendorName = $detail->vendor->name ?? 'N/A';
                                            $keperluan = $detail->keperluan ?? 'N/A';
                                            $jumlah = number_format($detail->jumlah_transfer, 0, ',', '.');

                                            $usedIndicator = in_array($detail->id, $usedDetailIds) ? ' (Tersedia kembali)' : '';

                                            $label = "{$vendorName} | {$keperluan} | Rp {$jumlah}{$usedIndicator}";

                                            return [$detail->id => $label];
                                        })->toArray();

                                    } catch (Exception $e) {
                                        Log::error('Error in nota_dinas_detail_id options: '.$e->getMessage());

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

                                        $usedDetailIds = PengeluaranLain::whereNotNull('nota_dinas_detail_id')
                                            ->when($get('id'), function ($query) use ($get) {
                                                return $query->where('id', '!=', $get('id'));
                                            })
                                            ->pluck('nota_dinas_detail_id')
                                            ->toArray();

                                        $actualUsedCount = count($usedDetailIds);
                                        $totalCount = NotaDinasDetail::where('nota_dinas_id', $notaDinasId)
                                            ->where('jenis_pengeluaran', 'lain-lain') // Filter hanya untuk jenis pengeluaran 'lain-lain'
                                            ->count();

                                        return "Pilih detail nota dinas 'Lain-lain' yang akan dibayar (Sudah dipilih: {$actualUsedCount}/{$totalCount})";

                                    } catch (Exception $e) {
                                        return 'Pilih detail nota dinas yang akan dibayar';
                                    }
                                })
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    try {
                                        if (! $state) {
                                            $set('vendor_id', null);
                                            $set('bank_name', null);
                                            $set('account_holder', null);
                                            $set('bank_account', null);
                                            $set('amount', null);
                                            $set('note', null);
                                            $set('name', null);

                                            return;
                                        }

                                        // Fetch NotaDinasDetail and populate related fields
                                        $notaDinasDetail = NotaDinasDetail::with('vendor')->find($state);
                                        if ($notaDinasDetail) {
                                            $set('vendor_id', $notaDinasDetail->vendor_id);
                                            $set('bank_name', $notaDinasDetail->bank_name ?? $notaDinasDetail->vendor->bank_name);
                                            $set('account_holder', $notaDinasDetail->account_holder ?? $notaDinasDetail->vendor->account_holder);
                                            $set('bank_account', $notaDinasDetail->bank_account ?? $notaDinasDetail->vendor->bank_account);
                                            $set('amount', self::safeFloatVal($notaDinasDetail->jumlah_transfer ?? 0));
                                            $set('note', $notaDinasDetail->keperluan ?? null);

                                            // Auto-populate name from keperluan + event
                                            $nameComponents = [];
                                            if ($notaDinasDetail->keperluan) {
                                                $nameComponents[] = $notaDinasDetail->keperluan;
                                            }
                                            if ($notaDinasDetail->event && $notaDinasDetail->event !== $notaDinasDetail->keperluan) {
                                                $nameComponents[] = $notaDinasDetail->event;
                                            }

                                            $autoName = ! empty($nameComponents) ? implode(' - ', $nameComponents) : 'Pengeluaran Lain';
                                            $set('name', $autoName);

                                            // Auto-populate no_nd from NotaDinas
                                            $notaDinas = $notaDinasDetail->notaDinas;
                                            if ($notaDinas) {
                                                $set('no_nd', $notaDinas->no_nd);
                                            }
                                        }
                                    } catch (Exception $e) {
                                        Log::error('Error in afterStateUpdated: '.$e->getMessage());
                                    }
                                })
                                ->required()
                                ->columnSpan(2),

                            Hidden::make('vendor_id'),
                        ]),

                    Grid::make(4)
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

                            DatePicker::make('tanggal_transfer')
                                ->label('Tanggal Transfer')
                                ->helperText(new HtmlString('<span style="color: #ef4444;">Sesuaikan tanggal transfer</span>'))
                                ->default(now())
                                ->required()
                                ->columnSpan(1),
                        ]),

                    Grid::make(3)
                        ->schema([
                            Select::make('payment_method_id')
                                ->relationship('paymentMethod', 'name')
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->is_cash ? 'Kas/Tunai' : ($record->bank_name ? "{$record->bank_name} - {$record->no_rekening}" : $record->name))
                                ->label('Sumber pembayaran')
                                ->searchable()
                                ->helperText(new HtmlString('<span style="color: #ef4444;">Sesuaikan rekening transfer</span>'))
                                ->preload()
                                ->required()
                                ->columnSpan(1),

                            DatePicker::make('date_expense')
                                ->label('Tanggal Pengeluaran')
                                ->date()
                                ->required()
                                ->native(false)
                                ->displayFormat('d M Y')
                                ->default(now())
                                ->columnSpan(1),

                            Select::make('kategori_transaksi')
                                ->options([
                                    'uang_keluar' => 'Uang Keluar',
                                ])
                                ->default('uang_keluar')
                                ->label('Tipe Transaksi')
                                ->required()
                                ->disabled()
                                ->columnSpan(1),
                        ]),

                    Grid::make(1)
                        ->schema([
                            TextInput::make('no_nd')
                                ->label('Nomor Nota Dinas')
                                ->required()
                                ->live()
                                ->helperText('Akan otomatis terisi setelah memilih detail nota dinas'),

                            Textarea::make('note')
                                ->label('Catatan Tambahan / Keperluan')
                                ->required()
                                ->rows(3)
                                ->live()
                                ->helperText('Akan otomatis terisi dari detail nota dinas, dapat diedit'),

                            FileUpload::make('image')
                                ->label('Bukti Pembayaran')
                                ->image()
                                ->imageEditor()
                                ->directory('pengeluaran-lain/'.date('Y/m'))
                                ->visibility('private')
                                ->downloadable()
                                ->openable()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                                ->maxSize(1280)
                                ->helperText('Max 1MB. JPG, PNG, or PDF format.')
                                ->required(),
                        ]),
                ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->tooltip('Expense Name')
                    ->description(fn (PengeluaranLain $record): ?string => Str::limit($record->note, 50))
                    ->toggleable(),

                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color('primary')
                    ->tooltip('Vendor/Supplier'),

                TextColumn::make('amount')
                    ->numeric()
                    ->label('Nominal')
                    ->prefix('Rp. ')
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->formatStateUsing(fn ($state): string => 'Total: Rp. '.number_format($state, 0, ',', '.')),
                    ])
                    ->toggleable(),

                TextColumn::make('kategori_transaksi')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'uang_masuk' => 'success',
                        'uang_keluar' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'uang_masuk' => 'Uang Masuk',
                        'uang_keluar' => 'Uang Keluar',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('paymentMethod.name')
                    ->label('Sumber Pembayaran')
                    ->toggleable()
                    ->formatStateUsing(function ($record) {
                        $method = $record->paymentMethod;
                        if (! $method) {
                            return 'N/A';
                        }

                        return $method->is_cash ? 'Kas/Tunai' : ($method->bank_name ? "{$method->bank_name}" : $method->name);
                    })
                    ->description(fn (PengeluaranLain $record): string => $record->paymentMethod?->no_rekening ?? 'N/A')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->tooltip('Metode Pembayaran (No. Rekening)'),

                TextColumn::make('date_expense')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->tooltip('Expense Date'),
                TextColumn::make('no_nd')
                    ->label('Nota Dinas')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Nomor nota dinas berhasil disalin')
                    ->tooltip('Document Number'),

                TextColumn::make('notaDinas.status')
                    ->label('Status ND')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('bank_name')
                    ->label('Bank Transfer')
                    ->searchable()
                    ->toggleable()
                    ->description(fn (PengeluaranLain $record): string => $record->account_holder ?? 'N/A')
                    ->tooltip('Bank & Account Holder'),

                TextColumn::make('tanggal_transfer')
                    ->label('Tgl Transfer')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->tooltip('Transfer Date'),

                ImageColumn::make('image')
                    ->alignCenter()
                    ->square()
                    ->label('Bukti')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->tooltip('Receipt Image'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->tooltip('Created Date'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Last Update'),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Deletion Date'),
            ])
            ->defaultSort('date_expense', 'desc')
            ->filters([
                SelectFilter::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Payment Method'),
                SelectFilter::make('kategori_transaksi')
                    ->options([
                        'uang_masuk' => 'Uang Masuk',
                        'uang_keluar' => 'Uang Keluar',
                    ])
                    ->multiple()
                    ->label('Transaction Category'),
                Filter::make('date_expense')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('date_from')
                                ->label('Date From'),
                            DatePicker::make('date_until')
                                ->label('Date Until')
                                ->default(now()),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'], fn (Builder $query, $date): Builder => $query->whereDate('date_expense', '>=', $date))
                            ->when($data['date_until'], fn (Builder $query, $date): Builder => $query->whereDate('date_expense', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['from'] = 'From '.Carbon::parse($data['date_from'])->toFormattedDateString();
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators['until'] = 'Until '.Carbon::parse($data['date_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    })
                    ->label('Expense Date Range'),
                SelectFilter::make('amount')
                    ->label('Amount Range')
                    ->options([
                        'low' => 'Low (< Rp. 1,000,000)',
                        'medium' => 'Medium (Rp. 1,000,000 - 5,000,000)',
                        'high' => 'High (> Rp. 5,000,000)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'low' => $query->where('amount', '<', 1000000),
                            'medium' => $query->whereBetween('amount', [1000000, 5000000]),
                            'high' => $query->where('amount', '>', 5000000),
                            default => $query,
                        };
                    }),
                TrashedFilter::make(),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth('lg')
                        ->tooltip('Lihat detail pengeluaran'),
                    EditAction::make()
                        ->tooltip('Edit pengeluaran'),
                    Action::make('duplicate')
                        ->label('Duplikat')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->action(function (PengeluaranLain $record, array $data): void {
                            PengeluaranLain::create([
                                'name' => $record->name.' (Copy)',
                                'amount' => $record->amount,
                                'payment_method_id' => $record->payment_method_id,
                                'date_expense' => now(),
                                'kategori_transaksi' => $record->kategori_transaksi,
                                'no_nd' => $record->no_nd + 1,
                                'note' => $record->note,
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Duplikat Pengeluaran Lain')
                        ->modalDescription('Apakah Anda yakin ingin menduplikat pengeluaran ini?')
                        ->tooltip('Duplikat pengeluaran ini'),
                    Action::make('download_receipt')
                        ->label('Download Bukti')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->url(fn (PengeluaranLain $record): ?string => $record->image ? Storage::url($record->image) : null, shouldOpenInNewTab: true)
                        ->visible(fn (PengeluaranLain $record): bool => $record->image !== null)
                        ->tooltip('Download bukti pembayaran'),
                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->tooltip('Hapus pengeluaran'),
                    RestoreAction::make()
                        ->tooltip('Pulihkan pengeluaran'),
                ])
                    ->tooltip('Aksi Pengeluaran')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('PERHATIAN: Data akan dihapus secara permanen dan tidak dapat dikembalikan!')
                        ->modalSubmitActionLabel('Ya, Hapus Permanen')
                        ->modalCancelActionLabel('Batal'),
                    BulkAction::make('export')
                        ->label('Export ke Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Simple CSV export if no Excel class exists
                            $filename = 'pengeluaran-lain-'.date('Y-m-d').'.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => "attachment; filename={$filename}",
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');
                                fputcsv($file, ['Nama', 'Jumlah', 'Tanggal', 'Kategori', 'No. ND', 'Catatan']);

                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->name,
                                        $record->amount,
                                        $record->date_expense,
                                        $record->kategori_transaksi,
                                        'ND-0'.$record->no_nd,
                                        $record->note,
                                    ]);
                                }
                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('mark_as_uang_keluar')
                        ->label('Tandai sebagai Uang Keluar')
                        ->icon('heroicon-o-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['kategori_transaksi' => 'uang_keluar']);
                            });
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ])->label('Aksi Massal'),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Buat Pengeluaran Lain Pertama')
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->emptyStateHeading('Belum Ada Pengeluaran Lain')
            ->emptyStateDescription('Mulai dengan membuat pengeluaran di luar operasional harian pertama Anda.')
            ->emptyStateIcon('heroicon-o-credit-card')
            ->poll('60s')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengeluaranLains::route('/'),
            'create' => CreatePengeluaranLain::route('/create'),
            'edit' => EditPengeluaranLain::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Menampilkan jumlah total pengeluaran di tahun 2025 sebagai badge
        return static::getModel()::whereYear('date_expense', 2025)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getModel()::whereYear('date_expense', 2025)->count();

        return match (true) {
            $count > 100 => 'danger',
            $count > 50 => 'warning',
            $count > 0 => 'success',
            default => 'gray'
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total pengeluaran lain tahun 2025 (di luar operasional harian)';
    }

    public static function getWidgets(): array
    {
        return [
            PengeluaranOverviewWidgets::class,
        ];
    }
}
