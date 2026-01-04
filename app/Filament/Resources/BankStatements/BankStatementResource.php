<?php

namespace App\Filament\Resources\BankStatements;

use App\Filament\Resources\BankStatements\Pages\CreateBankStatement;
use App\Filament\Resources\BankStatements\Pages\EditBankStatement;
use App\Filament\Resources\BankStatements\Pages\ListBankStatements;
use App\Filament\Resources\BankStatements\Pages\ReconciliationComparison;
use App\Filament\Resources\BankStatements\Pages\ViewBankStatement;
use App\Filament\Resources\BankStatements\RelationManagers\BankReconciliationItemsRelationManager;
use App\Filament\Resources\BankStatements\Widgets\BankStatementOverview;
use App\Models\BankStatement;
use App\Models\PaymentMethod;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BankStatementResource extends Resource
{
    protected static ?string $model = BankStatement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Rekening Koran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('BankStatementTabs')
                    ->tabs([
                        Tab::make('Informasi Utama')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Informasi Rekening')
                                    ->description('Pilih rekening bank yang akan digunakan untuk rekening koran')
                                    ->schema([
                                        Select::make('payment_method_id')
                                            ->relationship(
                                                'paymentMethod',
                                                'no_rekening',
                                                fn ($query) => $query->whereNotNull('no_rekening')
                                                    ->where('no_rekening', '!=', '')
                                                    ->whereNotNull('bank_name')
                                                    ->where('bank_name', '!=', '')
                                                    ->orderBy('bank_name')
                                                    ->orderBy('no_rekening')
                                            )
                                            ->searchable(['bank_name', 'no_rekening'])
                                            ->preload()
                                            ->required()
                                            ->label('Rekening Bank')
                                            ->placeholder('Pilih rekening bank...')
                                            ->helperText('Pilih rekening bank yang memiliki nomor rekening valid')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->no_rekening && $record->bank_name
                                                    ? "{$record->bank_name} - {$record->no_rekening}".
                                                      ($record->cabang ? " - {$record->name}" : '')
                                                    : 'Data rekening tidak lengkap'
                                            )
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nama Metode Pembayaran')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('bank_name')
                                                    ->label('Nama Bank')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Contoh: BCA, Mandiri, BNI'),
                                                TextInput::make('no_rekening')
                                                    ->label('Nomor Rekening')
                                                    ->required()
                                                    ->maxLength(50)
                                                    ->placeholder('Masukkan nomor rekening'),
                                                TextInput::make('cabang')
                                                    ->label('Cabang')
                                                    ->maxLength(255)
                                                    ->placeholder('Nama cabang (opsional)'),
                                            ])
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $paymentMethod = PaymentMethod::find($state);
                                                    if ($paymentMethod) {
                                                        $set('branch', $paymentMethod->cabang);
                
                                                        // Auto-fill some fields if available
                                                        if ($paymentMethod->bank_name) {
                                                            // You can add more auto-fill logic here
                                                        }
                                                    }
                                                } else {
                                                    $set('branch', null);
                                                }
                                            })
                                            ->live(),
                
                                        TextInput::make('branch')
                                            ->label('Cabang')
                                            ->maxLength(255)
                                            ->placeholder('Cabang akan terisi otomatis dari rekening yang dipilih')
                                            ->helperText('Informasi cabang dari rekening yang dipilih'),
                                    ])->columns(1),
                
                                Section::make('Periode Rekening Koran')
                                    ->description('Tentukan periode waktu untuk rekening koran')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                DatePicker::make('period_start')
                                                    ->label('Periode Awal')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('d M Y')
                                                    ->placeholder('Pilih tanggal mulai')
                                                    ->helperText('Tanggal awal periode rekening koran')
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        // Auto-set period_end to 30 days after period_start if not set
                                                        if ($state && ! $get('period_end')) {
                                                            $endDate = \Carbon\Carbon::parse($state)->addDays(29);
                                                            $set('period_end', $endDate->format('Y-m-d'));
                                                        }
                                                    }),
                                                DatePicker::make('period_end')
                                                    ->label('Periode Akhir')
                                                    ->required()
                                                    ->native(false)
                                                    ->displayFormat('d M Y')
                                                    ->placeholder('Pilih tanggal akhir')
                                                    ->helperText('Tanggal akhir periode rekening koran')
                                                    ->afterOrEqual('period_start'),
                                            ]),
                
                                        Actions::make([
                                            Action::make('set_current_month')
                                                ->label('Bulan Ini')
                                                ->icon('heroicon-o-calendar')
                                                ->color('primary')
                                                ->action(function (callable $set) {
                                                    $now = \Carbon\Carbon::now();
                                                    $set('period_start', $now->startOfMonth()->format('Y-m-d'));
                                                    $set('period_end', $now->endOfMonth()->format('Y-m-d'));
                                                }),
                                            Action::make('set_last_month')
                                                ->label('Bulan Lalu')
                                                ->icon('heroicon-o-calendar-days')
                                                ->color('gray')
                                                ->action(function (callable $set) {
                                                    $lastMonth = \Carbon\Carbon::now()->subMonth();
                                                    $set('period_start', $lastMonth->startOfMonth()->format('Y-m-d'));
                                                    $set('period_end', $lastMonth->endOfMonth()->format('Y-m-d'));
                                                }),
                                            Action::make('set_last_30_days')
                                                ->label('30 Hari Terakhir')
                                                ->icon('heroicon-o-clock')
                                                ->color('success')
                                                ->action(function (callable $set) {
                                                    $now = \Carbon\Carbon::now();
                                                    $set('period_start', $now->subDays(30)->format('Y-m-d'));
                                                    $set('period_end', $now->format('Y-m-d'));
                                                }),
                                        ])->extraAttributes(['class' => 'mt-4']),
                                    ]),
                            ]),
                        
                        Tab::make('File & Dokumen')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('File Rekening Koran')
                                    ->description('Upload file rekening koran dari bank')
                                    ->schema([
                                        Select::make('source_type')
                                            ->label('Tipe Sumber File')
                                            ->options([
                                                'pdf' => 'PDF - File rekening koran PDF dari bank',
                                                // 'excel' => 'Excel - File spreadsheet (.xlsx, .xls)',
                                                // 'manual_input' => 'Input Manual - Entry data secara manual',
                                            ])
                                            ->required()
                                            ->default('pdf')
                                            ->helperText('Pilih jenis file yang akan diupload')
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                // Clear file when changing source type
                                                if ($state === 'manual_input') {
                                                    $set('file_path', null);
                                                }
                                            }),
                
                                        FileUpload::make('file_path')
                                            ->label('Upload File Rekening Koran')
                                            ->disk('public')
                                            ->directory('bank-statements')
                                            ->acceptedFileTypes(function (callable $get) {
                                                $sourceType = $get('source_type');
                
                                                return match ($sourceType) {
                                                    'pdf' => ['application/pdf'],
                                                    'excel' => [
                                                        'application/vnd.ms-excel',
                                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                        'text/csv',
                                                    ],
                                                    default => ['application/pdf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                                                };
                                            })
                                            ->maxSize(10240) // 10MB
                                            ->helperText(function (callable $get) {
                                                $sourceType = $get('source_type');
                
                                                return match ($sourceType) {
                                                    'pdf' => 'Upload file PDF rekening koran (max 10MB)',
                                                    'excel' => 'Upload file Excel/CSV (max 10MB)',
                                                    'manual_input' => 'File tidak diperlukan untuk input manual',
                                                    default => 'Upload file rekening koran (max 10MB)'
                                                };
                                            })
                                            ->required(fn (callable $get) => $get('source_type') !== 'manual_input')
                                            ->visible(fn (callable $get) => $get('source_type') !== 'manual_input')
                                            ->deletable(true)
                                            ->downloadable()
                                            ->previewable(false)
                                            ->loadingIndicatorPosition('left')
                                            ->removeUploadedFileButtonPosition('right')
                                            ->uploadButtonPosition('left')
                                            ->uploadProgressIndicatorPosition('left'),
                
                                        Placeholder::make('file_info')
                                            ->label('Informasi File')
                                            ->content(function (callable $get, $livewire) {
                                                $record = $livewire->record ?? null;
                                                if ($record && $record->file_path) {
                                                    $filePath = storage_path('app/public/'.$record->file_path);
                                                    if (file_exists($filePath)) {
                                                        $fileSize = filesize($filePath);
                                                        $formattedSize = $fileSize > 1024 * 1024
                                                            ? round($fileSize / (1024 * 1024), 2).' MB'
                                                            : round($fileSize / 1024, 2).' KB';
                
                                                        return new HtmlString(
                                                            '<div class="space-y-2">'.
                                                            '<div><strong>Ukuran:</strong> '.$formattedSize.'</div>'.
                                                            '<div><strong>Diupload:</strong> '.$record->created_at->format('d M Y H:i').'</div>'.
                                                            '<div><a href="'.Storage::url($record->file_path).'" target="_blank" class="text-primary-600 hover:text-primary-700 underline font-medium">📄 Buka File</a></div>'.
                                                            '</div>'
                                                        );
                                                    }
                                                }
                
                                                return 'Belum ada file yang diupload';
                                            })
                                            ->visible(fn ($record) => $record && filled($record->file_path))
                                            ->extraAttributes(['class' => 'text-sm bg-gray-50 p-3 rounded-lg']),
                                    ]),
                            ]),
                        
                        Tab::make('Detail Keuangan')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Detail Finansial')
                                    ->description('Informasi saldo dan transaksi dari rekening koran')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('opening_balance')
                                                    ->label('Saldo Awal')
                                                    ->numeric()
                                                    ->prefix('IDR')
                                                    ->placeholder('0')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->inputMode('numeric')
                                                    ->helperText('Saldo awal periode'),
                
                                                TextInput::make('closing_balance')
                                                    ->label('Saldo Akhir')
                                                    ->numeric()
                                                    ->prefix('IDR')
                                                    ->placeholder('0')
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(',')
                                                    ->inputMode('numeric')
                                                    ->helperText('Saldo akhir periode'),
                
                                                Placeholder::make('balance_difference')
                                                    ->label('Selisih Saldo')
                                                    ->content(function (callable $get) {
                                                        $openingRaw = $get('opening_balance') ?? '';
                                                        $closingRaw = $get('closing_balance') ?? '';
                
                                                        // Handle both formatted (with dots) and raw numbers
                                                        $opening = $openingRaw ? (float) str_replace(['.', ',', ' '], '', $openingRaw) : 0;
                                                        $closing = $closingRaw ? (float) str_replace(['.', ',', ' '], '', $closingRaw) : 0;
                
                                                        $difference = $closing - $opening;
                
                                                        if ($difference == 0) {
                                                            return new HtmlString(
                                                                '<div class="text-gray-600 font-medium text-lg">IDR 0</div>'
                                                            );
                                                        }
                
                                                        $color = $difference > 0 ? 'text-green-600' : 'text-red-600';
                                                        $sign = $difference > 0 ? '+' : '';
                
                                                        return new HtmlString(
                                                            '<div class="'.$color.' font-semibold text-lg">'.
                                                            $sign.'IDR '.number_format($difference, 0, ',', '.').
                                                            '</div>'
                                                        );
                                                    }),
                                            ]),
                
                                        Fieldset::make('Transaksi Debit')
                                            ->schema([
                                                Grid::make(1)
                                                    ->schema([
                                                        TextInput::make('no_of_debit')
                                                            ->label('Jumlah Transaksi Debit')
                                                            ->numeric()
                                                            ->placeholder('0')
                                                            ->suffix('transaksi')
                                                            ->helperText('Total jumlah transaksi debit'),
                
                                                        TextInput::make('tot_debit')
                                                            ->label('Total Nominal Debit')
                                                            ->numeric()
                                                            ->prefix('IDR')
                                                            ->placeholder('0')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters(',')
                                                            ->inputMode('numeric')
                                                            ->helperText('Total nilai transaksi debit'),
                                                    ]),
                                            ]),
                
                                        Fieldset::make('Transaksi Kredit')
                                            ->schema([
                                                Grid::make(1)
                                                    ->schema([
                                                        TextInput::make('no_of_credit')
                                                            ->label('Jumlah Transaksi Kredit')
                                                            ->numeric()
                                                            ->placeholder('0')
                                                            ->suffix('transaksi')
                                                            ->helperText('Total jumlah transaksi kredit'),
                
                                                        TextInput::make('tot_credit')
                                                            ->label('Total Nominal Kredit')
                                                            ->numeric()
                                                            ->prefix('IDR')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->placeholder('0')
                                                            ->stripCharacters(',')
                                                            ->inputMode('numeric')
                                                            ->helperText('Total nilai transaksi kredit'),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                        
                        Tab::make('Rekonsiliasi')
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Section::make('Upload File Excel Rekonsiliasi')
                                    ->description('Upload file Excel untuk rekonsiliasi bank (opsional)')
                                    ->schema([
                                        FileUpload::make('reconciliation_file')
                                            ->label('File Excel Rekonsiliasi')
                                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                            ->disk('public')
                                            ->directory('bank-reconciliations')
                                            ->preserveFilenames()
                                            ->maxSize(10240) // 10MB
                                            ->helperText('Upload file Excel dengan format: Tanggal, Keterangan, Debit, Credit')
                                            ->live()
                                            ->required(fn (string $operation): bool => $operation === 'create')
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    // Set reconciliation original filename when file is uploaded
                                                    $set('reconciliation_original_filename', basename($state));
                                                }
                                            }),
                                    ])->columns(1)
                                    ->collapsible()
                                    ->collapsed(),
                
                                Section::make('Status Rekonsiliasi')
                                    ->schema([
                                        Select::make('reconciliation_status')
                                            ->label('Status Rekonsiliasi')
                                            ->options(BankStatement::getReconciliationStatusOptions())
                                            ->default('uploaded')
                                            ->disabled(fn (string $operation): bool => $operation === 'create'),
                
                                        TextInput::make('total_records')
                                            ->label('Total Records')
                                            ->numeric()
                                            ->disabled()
                                            ->default(0),
                
                                        TextInput::make('total_debit_reconciliation')
                                            ->label('Total Debit Rekonsiliasi')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->disabled()
                                            ->default(0),
                
                                        TextInput::make('total_credit_reconciliation')
                                            ->label('Total Credit Rekonsiliasi')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->disabled()
                                            ->default(0),
                                    ])->columns(2)
                                    ->collapsible()
                                    ->collapsed(),
                            ]),
                    ])->columnSpanFull(),

                Hidden::make('status')->default('pending'),

                Hidden::make('uploaded_by')
                    ->default(fn () => Auth::id()),

                Hidden::make('last_edited_by')
                    ->default(fn () => Auth::id()),

                Hidden::make('original_filename'),
                Hidden::make('reconciliation_original_filename'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period_start', 'desc') // Menambahkan default sorting
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('paymentMethod.no_rekening')
                    ->label('No. Rekening')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if ($record->paymentMethod) {
                            return $record->paymentMethod->bank_name.' - '.$record->paymentMethod->no_rekening;
                        }

                        return '-';
                    }),
                TextColumn::make('paymentMethod.name')
                    ->label('Pemilik'),
                TextColumn::make('period_start')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('period_end')
                    ->label('Tanggal Akhir')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('branch')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('opening_balance')
                    ->label('Saldo Awal')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('closing_balance')
                    ->label('Saldo Akhir')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('no_of_debit')
                    ->label('Jumlah Debit')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->suffix(' transaksi')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tot_debit')
                    ->label('Total Debit')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->color('danger'),
                TextColumn::make('no_of_credit')
                    ->label('Jumlah Kredit')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->suffix(' transaksi')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tot_credit')
                    ->label('Total Kredit')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->color('success'),
                TextColumn::make('source_type')
                    ->label('Tipe Sumber')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pdf' => 'danger',
                        'excel' => 'success',
                        'manual_input' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => BankStatement::getSourceTypeOptions()[$state] ?? $state),

                TextColumn::make('reconciliation_status')
                    ->label('Status Rekonsiliasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'uploaded' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => BankStatement::getReconciliationStatusOptions()[$state] ?? $state),

                TextColumn::make('total_records')
                    ->label('Total Records')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->suffix(' records')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reconciliation_original_filename')
                    ->label('File Rekonsiliasi')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        if (! $state || ! $record->reconciliation_file) {
                            return new HtmlString('<span class="text-gray-400">Tidak ada</span>');
                        }

                        $fileName = $state;
                        $url = Storage::url($record->reconciliation_file);

                        return new HtmlString(
                            '<div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="'.$url.'" target="_blank" class="text-blue-600 hover:text-blue-800 truncate max-w-32" title="'.htmlspecialchars($fileName).'">
                                    '.Str::limit(htmlspecialchars($fileName), 20).'
                                </a>
                            </div>'
                        );
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // Audit Trail Columns
                TextColumn::make('lastEditedBy.name')
                    ->label('Terakhir Diedit Oleh')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => $state ?? 'System'),

                TextColumn::make('updated_at')
                    ->label('Waktu Edit Terakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->tooltip(fn ($record) => $record->updated_at?->format('d F Y H:i:s')),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('d F Y H:i:s')),
            ])
            ->filters([
                SelectFilter::make('payment_method_id')
                    ->relationship(
                        'paymentMethod',
                        'no_rekening',
                        fn ($query) => $query->whereNotNull('no_rekening')->where('no_rekening', '!=', '')
                    )
                    ->label('Rekening Bank')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Pilih Rekening Bank')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->no_rekening ? ($record->bank_name.' - '.$record->no_rekening) : 'Nomor rekening tidak tersedia'),

                Filter::make('period_date')
                    ->schema([
                        DatePicker::make('period_start_from')
                            ->label('Periode Mulai Dari')
                            ->native(false),
                        DatePicker::make('period_end_until')
                            ->label('Periode Selesai Hingga')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['period_start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('period_start', '>=', $date),
                            )
                            ->when(
                                $data['period_end_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('period_end', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['period_start_from'] ?? null) {
                            $indicators['period_start_from'] = 'Periode mulai dari '.Carbon::parse($data['period_start_from'])->format('d M Y');
                        }
                        if ($data['period_end_until'] ?? null) {
                            $indicators['period_end_until'] = 'Periode selesai hingga '.Carbon::parse($data['period_end_until'])->format('d M Y');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('source_type')
                    ->label('Sumber File')
                    ->options(BankStatement::getSourceTypeOptions()),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BankStatement::getStatusOptions())
                    ->multiple(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat Detail')
                        ->color('info')
                        ->tooltip('Lihat detail rekening koran'),
                    EditAction::make()
                        ->label('Edit')
                        ->color('warning')
                        ->tooltip('Edit rekening koran'),
                    Action::make('reconcile_comparison')
                        ->label('Rekonsiliasi Perbandingan')
                        ->icon('heroicon-o-scale')
                        ->color('primary')
                        ->visible(fn (BankStatement $record): bool => $record->payment_method_id &&
                            $record->reconciliationItems()->count() > 0
                        )
                        ->tooltip('Bandingkan transaksi aplikasi dengan mutasi bank')
                        ->url(fn (BankStatement $record): string => route('bank-statements.reconciliation-alt', $record))
                        ->openUrlInNewTab(false),
                    Action::make('download')
                        ->label('Unduh File')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn (BankStatement $record): string => $record->file_path ? Storage::url($record->file_path) : '#')
                        ->openUrlInNewTab()
                        ->visible(fn (BankStatement $record): bool => ! empty($record->file_path))
                        ->tooltip('Unduh file rekening koran'),
                    DeleteAction::make()
                        ->label('Hapus')
                        ->color('danger')
                        ->tooltip('Hapus rekening koran')
                        ->modalHeading('Hapus Rekening Koran')
                        ->modalDescription('Apakah Anda yakin ingin menghapus rekening koran ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ])
                    ->tooltip('Aksi Rekening Koran')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Rekening Koran Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus rekening koran yang dipilih?')
                        ->modalSubmitActionLabel('Ya, hapus'),
                ])->label('Aksi Massal'),
            ])
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50])
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading('Belum ada rekening koran')
            ->emptyStateDescription('Mulai dengan membuat rekening koran pertama Anda untuk melacak transaksi keuangan.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat Rekening Koran Baru')
                    ->url(static::getUrl('create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ]);
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->withCount('transactions');
    // }

    public static function getRelations(): array
    {
        return [
            BankReconciliationItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankStatements::route('/'),
            'create' => CreateBankStatement::route('/create'),
            'view' => ViewBankStatement::route('/{record}'),
            'edit' => EditBankStatement::route('/{record}/edit'),
            'reconciliation' => ReconciliationComparison::route('/{record}/reconciliation'),
        ];
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_at'] = Carbon::now();
        if (! isset($data['total_records'])) {
            $data['total_records'] = 0;
        }
        if (! isset($data['reconciliation_status'])) {
            $data['reconciliation_status'] = 'uploaded';
        }
        if (! empty($data['file_path']) && empty($data['original_filename'])) {
            $data['original_filename'] = basename($data['file_path']);
        }
        foreach (['total_debit_reconciliation', 'total_credit_reconciliation'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = (float) str_replace(['.', ',', ' ', 'IDR'], '', $data[$field]) ?: 0;
            } elseif (! isset($data[$field])) {
                $data[$field] = 0;
            }
        }

        return $data;
    }

    protected static function mutateFormDataBeforeFill(array $data): array
    {
        // Pastikan field numerik diformat dengan benar saat dimuat untuk edit
        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        // Bersihkan masalah formatting sebelum menyimpan
        $numericFields = ['opening_balance', 'closing_balance', 'tot_debit', 'tot_credit'];

        foreach ($numericFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                // Hapus format dan konversi ke angka
                $data[$field] = (float) str_replace(['.', ',', ' ', 'IDR'], '', $data[$field]) ?: null;
            }
        }

        return $data;
    }

    public static function getNavigationBadge(): ?string
    {
        // Menampilkan jumlah total rekening koran sebagai badge
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        // Memberikan warna pada badge untuk visibilitas yang lebih baik
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total rekening koran yang terdaftar';
    }

    public static function getWidgets(): array
    {
        return [
            BankStatementOverview::class,
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user instanceof \App\Models\User) {
            return false;
        }

        return $user->can('ViewAny:BankStatement');
    }
}
