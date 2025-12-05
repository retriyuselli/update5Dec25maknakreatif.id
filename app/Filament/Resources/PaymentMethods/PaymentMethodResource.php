<?php

namespace App\Filament\Resources\PaymentMethods;

use App\Filament\Resources\PaymentMethods\Pages\CreatePaymentMethod;
use App\Filament\Resources\PaymentMethods\Pages\EditPaymentMethod;
use App\Filament\Resources\PaymentMethods\Pages\ListPaymentMethods;
use App\Filament\Resources\PaymentMethods\Widgets\PaymentMethodStatsWidget;
use App\Imports\BankStatementImport;
use App\Models\BankStatement;
use App\Models\PaymentMethod;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Daftar Rekening';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Rekening')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->placeholder('nama pemilik rekening')
                            ->maxLength(255),
                        TextInput::make('bank_name')
                            ->prefix('Bank ')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('cabang')
                            ->placeholder('cabang bank (opsional)')
                            ->maxLength(255),
                        TextInput::make('no_rekening')
                            ->required()
                            ->numeric(),
                        Toggle::make('is_cash')
                            ->required(),
                    ])->columns(2),
                Section::make('Saldo Awal')
                    ->description('Isi jika rekening ini memiliki saldo sebelum dicatat di sistem. Saldo ini akan menjadi titik awal perhitungan.')
                    ->schema([
                        TextInput::make('opening_balance')
                            ->label('Saldo Awal (Opening Balance)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0)
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        DatePicker::make('opening_balance_date')
                            ->label('Tanggal Saldo Awal')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d M Y'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                IconColumn::make('is_cash')
                    ->label('Tunai')
                    ->boolean()
                    ->trueIcon('heroicon-o-banknotes')
                    ->falseIcon('heroicon-o-credit-card')
                    ->trueColor('warning')
                    ->falseColor('info'),
                TextColumn::make('name')
                    ->label('Nama Rekening')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('no_rekening')
                    ->label('Nomor Rekening')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor rekening disalin')
                    ->fontFamily('mono'),
                TextColumn::make('opening_balance')
                    ->label('Saldo Awal')
                    ->money('idr')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('opening_balance_date')
                    ->label('Tgl Pembukuan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('saldo')
                    ->label('Saldo Saat Ini')
                    ->money('idr')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state == 0 ? 'warning' : 'success'))
                    ->description(fn ($record) => 'Perubahan: '.
                        ($record->perubahan_saldo >= 0 ? '+' : '').
                        'Rp '.number_format(abs($record->perubahan_saldo), 0, ',', '.')),
                TextColumn::make('status_perubahan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'naik' => 'success',
                        'turun' => 'danger',
                        'tetap' => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'naik' => 'heroicon-o-arrow-trending-up',
                        'turun' => 'heroicon-o-arrow-trending-down',
                        'tetap' => 'heroicon-o-minus',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'naik' => 'Naik',
                        'turun' => 'Turun',
                        'tetap' => 'Tetap',
                    }),
                TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_cash')
                    ->label('Tampilkan Hanya Uang Tunai')
                    ->query(fn (Builder $query): Builder => $query->where('is_cash', true))
                    ->toggle(),
                Filter::make('saldo_positif')
                    ->label('Saldo Positif')
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw('
                            (opening_balance + 
                            COALESCE((SELECT SUM(nominal) FROM data_pembayarans WHERE payment_method_id = payment_methods.id AND tgl_bayar >= opening_balance_date AND deleted_at IS NULL), 0) +
                            COALESCE((SELECT SUM(nominal) FROM pendapatan_lains WHERE payment_method_id = payment_methods.id AND tgl_bayar >= opening_balance_date AND deleted_at IS NULL), 0) -
                            COALESCE((SELECT SUM(amount) FROM expenses WHERE payment_method_id = payment_methods.id AND date_expense >= opening_balance_date AND deleted_at IS NULL), 0) -
                            COALESCE((SELECT SUM(amount) FROM expense_ops WHERE payment_method_id = payment_methods.id AND date_expense >= opening_balance_date AND deleted_at IS NULL), 0) -
                            COALESCE((SELECT SUM(amount) FROM pengeluaran_lains WHERE payment_method_id = payment_methods.id AND date_expense >= opening_balance_date AND deleted_at IS NULL), 0)
                            ) > 0
                        ');
                    }),
                Filter::make('saldo_negatif')
                    ->label('Saldo Negatif')
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw('
                            (opening_balance + 
                            COALESCE((SELECT SUM(nominal) FROM data_pembayarans WHERE payment_method_id = payment_methods.id AND tgl_bayar >= opening_balance_date AND deleted_at IS NULL), 0) +
                            COALESCE((SELECT SUM(nominal) FROM pendapatan_lains WHERE payment_method_id = payment_methods.id AND tgl_bayar >= opening_balance_date AND deleted_at IS NULL), 0) -
                            COALESCE((SELECT SUM(amount) FROM expenses WHERE payment_method_id = payment_methods.id AND date_expense >= opening_balance_date AND deleted_at IS NULL), 0) -
                            COALESCE((SELECT SUM(amount) FROM expense_ops WHERE payment_method_id = payment_methods.id AND date_expense >= opening_balance_date AND deleted_at IS NULL), 0) -
                            COALESCE((SELECT SUM(amount) FROM pengeluaran_lains WHERE payment_method_id = payment_methods.id AND date_expense >= opening_balance_date AND deleted_at IS NULL), 0)
                            ) < 0
                        ');
                    }),
            ])
            ->recordActions([
                Action::make('view_detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record]))
                    ->tooltip('Lihat detail lengkap rekening dengan tab Uang Masuk, Uang Keluar, dan Laporan'),
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('rekonsiliasi_bank')
                        ->label('Rekonsiliasi Bank')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn ($record) => ! $record->is_cash) // Hanya untuk rekening bank
                        ->schema([
                            Section::make('Upload Mutasi Bank')
                                ->description('Upload file Excel (.xlsx) atau CSV dari mutasi bank untuk melakukan rekonsiliasi otomatis')
                                ->schema([
                                    FileUpload::make('mutasi_file')
                                        ->label('File Mutasi Bank')
                                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/vnd.ms-excel'])
                                        ->helperText('Upload file Excel (.xlsx/.xls) atau CSV dari bank. 
                                                     ✅ Format yang didukung:
                                                     • Bank Mandiri: Balance History & Transaction History
                                                     • BCA, BNI, BRI: Format standar
                                                     • Format generic dengan kolom: Tanggal, Keterangan, Debit/Kredit, Saldo')
                                        ->required()
                                        ->disk('public')
                                        ->directory('bank-statements')
                                        ->preserveFilenames()
                                        ->maxSize(10240), // 10MB

                                    Grid::make(2)->schema([
                                        DatePicker::make('periode_dari')
                                            ->label('Periode Dari')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->default(now()->startOfMonth()),
                                        DatePicker::make('periode_sampai')
                                            ->label('Periode Sampai')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->default(now()->endOfMonth()),
                                    ]),

                                    Textarea::make('catatan')
                                        ->label('Catatan Rekonsiliasi')
                                        ->placeholder('Tambahkan catatan atau informasi khusus untuk rekonsiliasi ini...')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->action(function ($record, $data) {
                            try {
                                // Create Bank Statement record
                                $bankStatement = BankStatement::create([
                                    'payment_method_id' => $record->id,
                                    'period_start' => $data['periode_dari'],
                                    'period_end' => $data['periode_sampai'],
                                    'source_type' => 'excel',
                                    'file_path' => $data['mutasi_file'],
                                    'status' => 'pending',
                                    'uploaded_at' => now(),
                                    // Add default values for required fields
                                    'opening_balance' => 0,
                                    'closing_balance' => 0,
                                    'no_of_debit' => 0,
                                    'tot_debit' => 0,
                                    'no_of_credit' => 0,
                                    'tot_credit' => 0,
                                    'branch' => null,
                                ]);

                                // Import Excel file
                                $import = new BankStatementImport($bankStatement);
                                Excel::import($import, storage_path('app/public/'.$data['mutasi_file']));

                                $stats = $import->getImportStats();

                                // Determine final status
                                $hasErrors = ! empty($stats['errors']);
                                $finalStatus = $hasErrors ? 'failed' : 'parsed';

                                // Update bank statement with calculated statistics
                                if ($stats['imported'] > 0 && ! $hasErrors) {
                                    // Calculate transaction amounts from balance history (for Bank Mandiri format)
                                    $import->calculateTransactionAmounts();

                                    // Update bank statement statistics
                                    $import->updateBankStatementStatistics();
                                }

                                // Update bank statement status
                                $bankStatement->update([
                                    'status' => $finalStatus,
                                ]);

                                if ($hasErrors) {
                                    Notification::make()
                                        ->title('Import Selesai dengan Error')
                                        ->body("Berhasil import {$stats['imported']} transaksi. ".
                                              "Namun ada {$stats['skipped']} baris yang error: ".
                                              implode(', ', array_slice($stats['errors'], 0, 3)).
                                              (count($stats['errors']) > 3 ? '...' : ''))
                                        ->warning()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Import Bank Statement Berhasil')
                                        ->body("Berhasil import {$stats['imported']} transaksi. ".
                                              ($stats['skipped'] ? "Ada {$stats['skipped']} transaksi yang dilewati." : ''))
                                        ->success()
                                        ->send();
                                }

                            } catch (Exception $e) {
                                // Log the full error for debugging
                                Log::error('Bank Reconciliation Import Error', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'payment_method_id' => $record->id,
                                    'file' => $data['mutasi_file'] ?? 'unknown',
                                ]);

                                Notification::make()
                                    ->title('Error Rekonsiliasi Bank')
                                    ->body('Gagal memproses file: '.$e->getMessage().
                                          '. Pastikan format file Excel sesuai dengan template yang diharapkan.')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalHeading('Rekonsiliasi Bank')
                        ->modalDescription('Upload file mutasi bank untuk melakukan rekonsiliasi otomatis dengan transaksi sistem')
                        ->modalWidth('2xl'),
                    Action::make('export_transaksi')
                        ->label('Export Transaksi')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function ($record) {
                            Notification::make()
                                ->title('Export Transaksi')
                                ->body('Fitur export akan segera tersedia.')
                                ->info()
                                ->send();
                        }),
                ])
                    ->label('Aksi Lainnya')
                    ->color('gray')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-credit-card')
            ->emptyStateHeading('Tidak ada rekening ditemukan')
            ->emptyStateDescription('Silakan buat rekening baru untuk memulai mencatat transaksi keuangan.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Buat Rekening Baru')
                    ->url(static::getUrl('create'))
                    ->icon('heroicon-o-plus')
                    ->button(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([10, 25, 50])
            ->striped()
            ->description('Kelola semua rekening bank dan kas tunai. Saldo dihitung otomatis berdasarkan transaksi masuk dan keluar.');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PaymentMethodStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentMethods::route('/'),
            'create' => CreatePaymentMethod::route('/create'),
            'view' => Pages\PaymentMethod::route('/{record}'),
            'edit' => EditPaymentMethod::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->can('ViewAny:PaymentMethod');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total rekening bank & kas';
    }
}
