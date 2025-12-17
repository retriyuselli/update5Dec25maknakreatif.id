<?php

namespace App\Filament\Resources\NotaDinasDetails;

use App\Enums\OrderStatus;
use App\Enums\PengeluaranJenis;
use App\Filament\Resources\NotaDinasDetails\Pages\CreateNotaDinasDetail;
use App\Filament\Resources\NotaDinasDetails\Pages\EditNotaDinasDetail;
use App\Filament\Resources\NotaDinasDetails\Pages\ListNotaDinasDetails;
use App\Models\Expense;
use App\Models\ExpenseOps;
use App\Models\NotaDinasDetail;
use App\Models\Order;
use App\Models\PengeluaranLain;
use App\Models\User;
use App\Models\Vendor;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class NotaDinasDetailResource extends Resource
{
    protected static ?string $model = NotaDinasDetail::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Detail Nota Dinas';

    protected static ?string $modelLabel = 'Detail Nota Dinas';

    protected static ?string $pluralModelLabel = 'Detail Nota Dinas';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ND & Vendor')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Select::make('nota_dinas_id')
                                    ->label('Nota Dinas')
                                    ->relationship('notaDinas', 'no_nd')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('vendor_id')
                                    ->label('Vendor')
                                    ->relationship('vendor', 'name', fn ($query) => $query->where('status', 'vendor'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $vendor = Vendor::find($state);
                                        if ($vendor) {
                                            $set('bank_name', $vendor->bank_name);
                                            $set('bank_account', $vendor->bank_account);
                                            $set('account_holder', $vendor->account_holder);
                                        }
                                    })
                                    ->suffixAction(
                                        Action::make('openVendor')
                                            ->label('Edit Vendor')
                                            ->icon('heroicon-o-pencil-square')
                                            ->color('primary')
                                            ->url(fn ($state): string => $state ? route('filament.admin.resources.vendors.edit', ['record' => $state]) : '#')
                                            ->openUrlInNewTab()
                                            ->visible(fn ($state): bool => ! empty($state))
                                    )
                                    ->createOptionForm([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Nama Vendor')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, callable $set, ?Vendor $record) {
                                                        if ($state === null) {
                                                            $set('slug', '');

                                                            return;
                                                        }

                                                        $slug = Str::slug($state);

                                                        $exists = Vendor::where('slug', $slug)
                                                            ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                                            ->exists();

                                                        if ($exists) {
                                                            $slug = $slug.'-'.now()->timestamp;
                                                        }

                                                        $set('slug', $slug);
                                                    })
                                                    ->placeholder('Contoh: Studio Foto Makmur'),

                                                TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required()
                                                    ->helperText('Auto-generated dari nama vendor'),

                                                Select::make('category_id')
                                                    ->label('Kategori')
                                                    ->relationship('category', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->placeholder('Pilih kategori vendor'),

                                                Select::make('status')
                                                    ->label('Status')
                                                    ->options([
                                                        'vendor' => 'Vendor',
                                                        'product' => 'Product',
                                                    ])
                                                    ->default('vendor')
                                                    ->required(),

                                                TextInput::make('pic_name')
                                                    ->label('Contact Person')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Nama PIC/Contact Person'),

                                                TextInput::make('phone')
                                                    ->label('No. Telepon')
                                                    ->tel()
                                                    ->required()
                                                    ->prefix('+62')
                                                    ->maxLength(255)
                                                    ->placeholder('812XXXXXXXX')
                                                    ->helperText('Tanpa angka 0 di depan'),

                                                Textarea::make('address')
                                                    ->label('Alamat')
                                                    ->required()
                                                    ->rows(2)
                                                    ->columnSpanFull()
                                                    ->placeholder('Alamat lengkap vendor'),

                                                Textarea::make('description')
                                                    ->label('Deskripsi Singkat')
                                                    ->rows(3)
                                                    ->columnSpanFull()
                                                    ->maxLength(500)
                                                    ->placeholder('Deskripsi singkat tentang vendor dan layanannya'),
                                            ]),

                                        Section::make('Informasi Bank')
                                            ->description('Data rekening untuk transfer pembayaran')
                                            ->schema([
                                                Grid::make()
                                                    ->columns(2)
                                                    ->schema([
                                                        TextInput::make('bank_name')
                                                            ->label('Nama Bank')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->prefix('Bank ')
                                                            ->placeholder('BCA / Mandiri / BNI'),

                                                        TextInput::make('bank_account')
                                                            ->label('Nomor Rekening')
                                                            ->required()
                                                            ->numeric()
                                                            ->maxLength(255)
                                                            ->placeholder('1234567890'),

                                                        TextInput::make('account_holder')
                                                            ->label('Nama')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpanFull()
                                                            ->placeholder('Nama sesuai rekening bank')
                                                            ->helperText('Masukkan nama persis seperti di rekening bank'),
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Section::make('Rekening')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('account_holder')
                                    ->label('Nama')
                                    ->required()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->maxLength(255)
                                    ->placeholder('Otomatis terisi'),

                                TextInput::make('bank_name')
                                    ->label('Nama Bank')
                                    ->dehydrated()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->placeholder('Otomatis terisi')
                                    ->required(),

                                TextInput::make('bank_account')
                                    ->label('Nomor Rekening')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->maxLength(255)
                                    ->placeholder('Otomatis terisi')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        TextInput::make('keperluan')
                            ->label('Keperluan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Misal: Dekorasi, Catering, Fotografer'),

                        Grid::make(3)->schema([
                            Select::make('jenis_pengeluaran')
                                ->label('Jenis Pengeluaran')
                                ->options([
                                    PengeluaranJenis::WEDDING->value => 'Wedding',
                                    PengeluaranJenis::OPERASIONAL->value => 'Operasional',
                                    PengeluaranJenis::LAIN_LAIN->value => 'Lain-lain',
                                ])
                                ->required()
                                ->default(PengeluaranJenis::WEDDING->value)
                                ->live(),

                            Select::make('payment_stage')
                                ->label('Tahap Pembayaran')
                                ->options([
                                    'DP' => 'DP (Down Payment)',
                                    'Payment 1' => 'Payment 1',
                                    'Payment 2' => 'Payment 2',
                                    'Payment 3' => 'Payment 3',
                                    'Final Payment' => 'Final Payment',
                                    'Additional' => 'Additional',
                                ])
                                ->default('DP')
                                ->live(),

                            TextInput::make('jumlah_transfer')
                                ->label('Jumlah Transfer')
                                ->required()
                                ->numeric()
                                ->prefix('Rp. ')
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->placeholder('0'),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('order_id')
                                ->label('Event (Order)')
                                ->relationship('order', 'name', fn ($query) => $query->whereIn('status', [OrderStatus::Processing, OrderStatus::Done]))
                                ->searchable()
                                ->preload()
                                ->visible(fn (Get $get): bool => $get('jenis_pengeluaran') === PengeluaranJenis::WEDDING->value)
                                ->placeholder('Pilih order (Processing / Done)'),

                            TextInput::make('event')
                                ->label('Event')
                                ->maxLength(255)
                                ->placeholder('Nama event/acara')
                                ->visible(fn (Get $get): bool => $get('jenis_pengeluaran') !== PengeluaranJenis::WEDDING->value),
                        ]),
                    ]),

                Section::make('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('invoice_number')
                                ->label('Nomor Invoice')
                                ->maxLength(255)
                                ->placeholder('INV-001'),

                            Select::make('status_invoice')
                                ->label('Status Invoice')
                                ->options([
                                    'belum_dibayar' => 'Belum Dibayar',
                                    'menunggu' => 'Menunggu Pembayaran',
                                    'sudah_dibayar' => 'Sudah Dibayar',
                                ])
                                ->default('belum_dibayar')
                                ->required(),
                        ]),

                        FileUpload::make('invoice_file')
                            ->label('File Invoice')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(5120)
                            ->directory('nota-dinas/invoices')
                            ->visibility('private')
                            ->downloadable()
                            ->previewable(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('order') // Eager load the order relationship
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_status')
                    ->label('Status di Expense')
                    ->state(function (?NotaDinasDetail $record): string {
                        if (! $record) {
                            return '-';
                        }

                        $statuses = [];

                        // Check if exists in expenses table
                        $expense = Expense::where('nota_dinas_detail_id', $record->id)->first();
                        if ($expense) {
                            $statuses[] = 'Expense (Wedding)';
                        }

                        // Check if exists in expense_ops table
                        $expenseOps = ExpenseOps::where('nota_dinas_detail_id', $record->id)->first();
                        if ($expenseOps) {
                            $statuses[] = 'Expense Ops';
                        }

                        // Check if exists in pengeluaran_lains table
                        $pengeluaranLain = PengeluaranLain::where('nota_dinas_detail_id', $record->id)->first();
                        if ($pengeluaranLain) {
                            $statuses[] = 'Pengeluaran Lain';
                        }

                        if (empty($statuses)) {
                            return 'Belum Masuk';
                        }

                        return implode(', ', $statuses);
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state === 'Belum Masuk' => 'gray',
                        str_contains($state, 'Expense (Wedding)') => 'success',
                        str_contains($state, 'Expense Ops') => 'info',
                        str_contains($state, 'Pengeluaran Lain') => 'warning',
                        default => 'primary',
                    })
                    ->icon(fn (string $state): string => match (true) {
                        $state === 'Belum Masuk' => 'heroicon-o-clock',
                        str_contains($state, 'Expense') => 'heroicon-o-check-circle',
                        str_contains($state, 'Pengeluaran') => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->searchable(false)
                    ->sortable(false),
                TextColumn::make('notaDinas.no_nd')
                    ->label('No. ND')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->limit(25),
                TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('jenis_pengeluaran')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'wedding' => 'success',
                        'operasional' => 'info',
                        'lain-lain' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wedding' => 'Wedding',
                        'operasional' => 'Operasional',
                        'lain-lain' => 'Lain-lain',
                        default => $state,
                    }),
                TextColumn::make('payment_stage')
                    ->label('Tahap')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'DP' => 'warning',
                        'Payment 1' => 'info',
                        'Payment 2' => 'info',
                        'Payment 3' => 'info',
                        'Final Payment' => 'success',
                        'Additional' => 'gray',
                        default => 'primary',
                    })
                    ->placeholder('-'),
                TextColumn::make('event_display')
                    ->label('Event')
                    ->state(function (?NotaDinasDetail $record): string {
                        if (! $record) {
                            return '-';
                        }
                        if ($record->jenis_pengeluaran === 'wedding') {
                            return $record->order?->name ?? '-';
                        }

                        return $record->event ?? '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->where('event', 'like', "%{$search}%")
                                ->orWhereHas('order', function (Builder $subQuery) use ($search) {
                                    $subQuery->where('name', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->limit(20)
                    ->placeholder('-'),
                TextColumn::make('jumlah_transfer')
                    ->label('Jumlah Transfer')
                    ->prefix('Rp. ')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Sum::make()
                            ->money('Rp.')
                            ->label('Total Keseluruhan'),
                        Average::make()
                            ->money('Rp.')
                            ->label('Rata-rata'),
                    ]),
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->placeholder('-')
                    ->copyable(),
                IconColumn::make('invoice_file')
                    ->label('Invoice')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('status_invoice')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_dibayar' => 'danger',
                        'menunggu' => 'warning',
                        'sudah_dibayar' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'belum_dibayar' => 'heroicon-o-clock',
                        'menunggu' => 'heroicon-o-exclamation-triangle',
                        'sudah_dibayar' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                TextColumn::make('notaDinas.status')
                    ->label('Status ND')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        'dibayar' => 'primary',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('nota_dinas_id')
                    ->label('No. ND')
                    ->relationship('notaDinas', 'no_nd')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jenis_pengeluaran')
                    ->label('Jenis Pengeluaran')
                    ->options([
                        PengeluaranJenis::WEDDING->value => 'Wedding',
                        PengeluaranJenis::OPERASIONAL->value => 'Operasional',
                        PengeluaranJenis::LAIN_LAIN->value => 'Lain-lain',
                    ]),
                SelectFilter::make('status_invoice')
                    ->label('Status Invoice')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'menunggu' => 'Menunggu Pembayaran',
                        'sudah_dibayar' => 'Sudah Dibayar',
                    ]),
                SelectFilter::make('nota_dinas_status')
                    ->label('Status ND')
                    ->relationship('notaDinas', 'status')
                    ->options([
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'dibayar' => 'Dibayar',
                        'ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('vendor')
                    ->label('Vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('expense_status')
                    ->label('Status di Expense')
                    ->options([
                        'belum_masuk' => 'Belum Masuk',
                        'sudah_masuk' => 'Sudah Masuk',
                        'expense_wedding' => 'Expense (Wedding)',
                        'expense_ops' => 'Expense Ops',
                        'pengeluaran_lain' => 'Pengeluaran Lain',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! isset($data['value']) || $data['value'] === null) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'belum_masuk' => $query->whereDoesntHave('expenses')
                                ->whereDoesntHave('expenseOps')
                                ->whereDoesntHave('pengeluaranLains'),
                            'sudah_masuk' => $query->where(function (Builder $q) {
                                $q->whereHas('expenses')
                                    ->orWhereHas('expenseOps')
                                    ->orWhereHas('pengeluaranLains');
                            }),
                            'expense_wedding' => $query->whereHas('expenses'),
                            'expense_ops' => $query->whereHas('expenseOps'),
                            'pengeluaran_lain' => $query->whereHas('pengeluaranLains'),
                            default => $query,
                        };
                    }),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('mark_paid')
                    ->label('Tandai Dibayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (NotaDinasDetail $record): bool => $record->status_invoice !== 'sudah_dibayar' &&
                        $record->notaDinas->status === 'disetujui'
                    )
                    ->requiresConfirmation()
                    ->action(function (NotaDinasDetail $record): void {
                        $record->update(['status_invoice' => 'sudah_dibayar']);

                        // Check if all details are paid, update ND status
                        $allPaid = $record->notaDinas->details()
                            ->where('status_invoice', '!=', 'sudah_dibayar')
                            ->count() === 0;

                        if ($allPaid) {
                            $record->notaDinas->update(['status' => 'dibayar']);
                        }
                    }),
                Action::make('download_invoice')
                    ->label('Download Invoice')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->visible(fn (NotaDinasDetail $record): bool => ! empty($record->invoice_file))
                    ->url(fn (NotaDinasDetail $record): string => Storage::url($record->invoice_file)
                    )
                    ->openUrlInNewTab(),
                Action::make('view_expense_records')
                    ->label('Lihat Record Expense')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(function (NotaDinasDetail $record): bool {
                        $hasExpense = Expense::where('nota_dinas_detail_id', $record->id)->exists();
                        $hasExpenseOps = ExpenseOps::where('nota_dinas_detail_id', $record->id)->exists();
                        $hasPengeluaranLain = PengeluaranLain::where('nota_dinas_detail_id', $record->id)->exists();

                        return $hasExpense || $hasExpenseOps || $hasPengeluaranLain;
                    })
                    ->modalHeading(fn (NotaDinasDetail $record): string => 'Record Expense - '.$record->keperluan)
                    ->modalDescription('Detail record yang terkait dengan Nota Dinas Detail ini')
                    ->modalContent(function (NotaDinasDetail $record): HtmlString {
                        $content = '<div class="space-y-4">';

                        // Expense (Wedding)
                        $expenses = Expense::where('nota_dinas_detail_id', $record->id)->get();
                        if ($expenses->count() > 0) {
                            $content .= '<div class="bg-green-50 border border-green-200 rounded-lg p-4">';
                            $content .= '<h3 class="font-semibold text-green-800 mb-2">💒 Expense (Wedding) - '.$expenses->count().' record</h3>';
                            foreach ($expenses as $expense) {
                                $content .= '<div class="border-l-4 border-green-400 pl-3 py-2 bg-white rounded mb-2">';
                                $content .= '<p class="text-sm font-medium">Vendor: '.($expense->vendor?->name ?? 'N/A').'</p>';
                                $content .= '<p class="text-sm text-gray-600">Order: '.($expense->order?->name ?? 'N/A').'</p>';
                                $content .= '<p class="text-sm text-gray-600">Amount: Rp '.number_format($expense->amount, 0, ',', '.').'</p>';
                                $content .= '<p class="text-xs text-gray-500">Created: '.$expense->created_at->format('d-m-Y H:i').'</p>';
                                $content .= '</div>';
                            }
                            $content .= '</div>';
                        }

                        // Expense Ops
                        $expenseOps = ExpenseOps::where('nota_dinas_detail_id', $record->id)->get();
                        if ($expenseOps->count() > 0) {
                            $content .= '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
                            $content .= '<h3 class="font-semibold text-blue-800 mb-2">🏢 Expense Ops - '.$expenseOps->count().' record</h3>';
                            foreach ($expenseOps as $ops) {
                                $content .= '<div class="border-l-4 border-blue-400 pl-3 py-2 bg-white rounded mb-2">';
                                $content .= '<p class="text-sm font-medium">Vendor: '.($ops->vendor?->name ?? 'N/A').'</p>';
                                $content .= '<p class="text-sm text-gray-600">Description: '.($ops->description ?? 'N/A').'</p>';
                                $content .= '<p class="text-sm text-gray-600">Amount: Rp '.number_format($ops->amount, 0, ',', '.').'</p>';
                                $content .= '<p class="text-xs text-gray-500">Created: '.$ops->created_at->format('d-m-Y H:i').'</p>';
                                $content .= '</div>';
                            }
                            $content .= '</div>';
                        }

                        // Pengeluaran Lain
                        $pengeluaranLains = PengeluaranLain::where('nota_dinas_detail_id', $record->id)->get();
                        if ($pengeluaranLains->count() > 0) {
                            $content .= '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">';
                            $content .= '<h3 class="font-semibold text-yellow-800 mb-2">📋 Pengeluaran Lain - '.$pengeluaranLains->count().' record</h3>';
                            foreach ($pengeluaranLains as $lain) {
                                $content .= '<div class="border-l-4 border-yellow-400 pl-3 py-2 bg-white rounded mb-2">';
                                $content .= '<p class="text-sm font-medium">Description: '.($lain->description ?? 'N/A').'</p>';
                                $content .= '<p class="text-sm text-gray-600">Category: '.($lain->category ?? 'N/A').'</p>';
                                $content .= '<p class="text-sm text-gray-600">Amount: Rp '.number_format($lain->amount, 0, ',', '.').'</p>';
                                $content .= '<p class="text-xs text-gray-500">Created: '.$lain->created_at->format('d-m-Y H:i').'</p>';
                                $content .= '</div>';
                            }
                            $content .= '</div>';
                        }

                        $content .= '</div>';

                        return new HtmlString($content);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(function (NotaDinasDetail $record): bool {
                            /** @var User $user */
                            $user = Auth::user();
                            $hasPermission = ($user ? $user->hasRole('super_admin') : false) && ! $record->trashed();
                            $hasNoExpenseRelations = ! $record->expenses()->exists() &&
                                                   ! $record->expenseOps()->exists() &&
                                                   ! $record->pengeluaranLains()->exists();

                            return $hasPermission && $hasNoExpenseRelations;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Detail Nota Dinas')
                        ->modalDescription('Apakah Anda yakin ingin menghapus detail nota dinas ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger'),
                    Action::make('cannot_delete')
                        ->label('Tidak Dapat Dihapus')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('warning')
                        ->visible(function (NotaDinasDetail $record): bool {
                            /** @var User $user */
                            $user = Auth::user();
                            $hasPermission = ($user ? $user->hasRole('super_admin') : false) && ! $record->trashed();
                            $hasExpenseRelations = $record->expenses()->exists() ||
                                                 $record->expenseOps()->exists() ||
                                                 $record->pengeluaranLains()->exists();

                            return $hasPermission && $hasExpenseRelations;
                        })
                        ->modalHeading(fn (NotaDinasDetail $record): string => 'Detail Nota Dinas Tidak Dapat Dihapus - '.$record->keperluan)
                        ->modalDescription('Detail nota dinas ini memiliki relasi dengan expense dan tidak dapat dihapus.')
                        ->modalContent(function (NotaDinasDetail $record): HtmlString {
                            $content = '<div class="space-y-4">';

                            $content .= '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
                            $content .= '<h3 class="font-semibold text-red-800 mb-2">🚫 Penghapusan Diblokir</h3>';
                            $content .= '<p class="text-sm text-red-700 mb-3">Detail nota dinas ini tidak dapat dihapus karena memiliki relasi dengan expense records:</p>';

                            $reasons = [];

                            if ($record->expenses()->exists()) {
                                $count = $record->expenses()->count();
                                $reasons[] = "• {$count} record di Expense (Wedding)";
                            }

                            if ($record->expenseOps()->exists()) {
                                $count = $record->expenseOps()->count();
                                $reasons[] = "• {$count} record di Expense Ops";
                            }

                            if ($record->pengeluaranLains()->exists()) {
                                $count = $record->pengeluaranLains()->count();
                                $reasons[] = "• {$count} record di Pengeluaran Lain";
                            }

                            $content .= '<div class="bg-white rounded p-3">';
                            $content .= implode('<br>', $reasons);
                            $content .= '</div>';
                            $content .= '</div>';

                            $content .= '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
                            $content .= '<h3 class="font-semibold text-blue-800 mb-2">💡 Solusi</h3>';
                            $content .= '<p class="text-sm text-blue-700">Untuk menghapus detail nota dinas ini, hapus terlebih dahulu semua expense records yang terkait di:</p>';
                            $content .= '<ul class="text-sm text-blue-700 mt-2 ml-4">';
                            $content .= '<li>• Menu Expense (Wedding)</li>';
                            $content .= '<li>• Menu Expense Ops</li>';
                            $content .= '<li>• Menu Pengeluaran Lain</li>';
                            $content .= '</ul>';
                            $content .= '</div>';

                            $content .= '</div>';

                            return new HtmlString($content);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
                RestoreAction::make()
                    ->visible(function (NotaDinasDetail $record): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return ($user ? $user->hasRole('super_admin') : false) && $record->trashed();
                    }),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Permanen Detail Nota Dinas')
                    ->modalDescription('Apakah Anda yakin ingin MENGHAPUS PERMANEN detail nota dinas ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, hapus permanen')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->modalIconColor('danger')
                    ->visible(function (NotaDinasDetail $record): bool {
                        /** @var User $user */
                        $user = Auth::user();

                        return ($user ? $user->hasRole('super_admin') : false) && $record->trashed();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function (): bool {
                            /** @var User $user */
                            $user = Auth::user();

                            return $user ? $user->hasRole('super_admin') : false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Detail Nota Dinas Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus detail nota dinas yang dipilih? Hanya record tanpa relasi expense yang akan dihapus.')
                        ->modalSubmitActionLabel('Ya, hapus yang dapat dihapus')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->before(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $protectedRecords = [];
                            $deletableCount = 0;

                            foreach ($selectedRecords as $record) {
                                $hasExpenseRelations = $record->expenses()->exists() ||
                                                     $record->expenseOps()->exists() ||
                                                     $record->pengeluaranLains()->exists();

                                if ($hasExpenseRelations) {
                                    $expenseTypes = [];
                                    if ($record->expenses()->exists()) {
                                        $expenseTypes[] = 'Expense';
                                    }
                                    if ($record->expenseOps()->exists()) {
                                        $expenseTypes[] = 'Expense Ops';
                                    }
                                    if ($record->pengeluaranLains()->exists()) {
                                        $expenseTypes[] = 'Pengeluaran Lain';
                                    }

                                    $protectedRecords[] = $record->keperluan.' (ada di: '.implode(', ', $expenseTypes).')';
                                } else {
                                    $deletableCount++;
                                }
                            }

                            if (! empty($protectedRecords)) {
                                $message = "Detail nota dinas berikut tidak dapat dihapus karena memiliki relasi expense:\n\n";
                                $message .= '• '.implode("\n• ", $protectedRecords);

                                if ($deletableCount > 0) {
                                    $message .= "\n\n{$deletableCount} record tanpa relasi expense akan dihapus.";
                                }

                                Notification::make()
                                    ->warning()
                                    ->title('Beberapa Record Dilindungi')
                                    ->body($message)
                                    ->persistent()
                                    ->send();
                            }
                        })
                        ->action(function ($livewire) {
                            $selectedRecords = $livewire->getSelectedTableRecords();
                            $deletedCount = 0;
                            $protectedCount = 0;

                            foreach ($selectedRecords as $record) {
                                $hasExpenseRelations = $record->expenses()->exists() ||
                                                     $record->expenseOps()->exists() ||
                                                     $record->pengeluaranLains()->exists();

                                if (! $hasExpenseRelations) {
                                    try {
                                        $record->delete();
                                        $deletedCount++;
                                    } catch (Exception $e) {
                                        // Log error but continue with other records
                                    }
                                } else {
                                    $protectedCount++;
                                }
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Penghapusan Massal Selesai')
                                    ->body("{$deletedCount} detail nota dinas berhasil dihapus.".
                                           ($protectedCount > 0 ? " {$protectedCount} record dilindungi dari penghapusan." : ''))
                                    ->send();
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title('Tidak Ada Record Dihapus')
                                    ->body('Semua record yang dipilih memiliki relasi expense dan tidak dapat dihapus.')
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    RestoreBulkAction::make()
                        ->visible(function (): bool {
                            /** @var User $user */
                            $user = Auth::user();

                            return $user ? $user->hasRole('super_admin') : false;
                        })
                        ->deselectRecordsAfterCompletion(),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen Detail Nota Dinas Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin MENGHAPUS PERMANEN detail nota dinas yang dipilih? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.')
                        ->modalSubmitActionLabel('Ya, hapus permanen')
                        ->modalIcon('heroicon-o-exclamation-triangle')
                        ->modalIconColor('danger')
                        ->deselectRecordsAfterCompletion()
                        ->visible(function (): bool {
                            /** @var User $user */
                            $user = Auth::user();

                            return $user ? $user->hasRole('super_admin') : false;
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading()
            ->striped()
            ->extremePaginationLinks();
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
            'index' => ListNotaDinasDetails::route('/'),
            'create' => CreateNotaDinasDetail::route('/create'),
            'edit' => EditNotaDinasDetail::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total detail nota dinas';
    }
}
