<?php

namespace App\Filament\Resources\JournalBatches;

use App\Filament\Resources\JournalBatches\Pages\CreateJournalBatch;
use App\Filament\Resources\JournalBatches\Pages\EditJournalBatch;
use App\Filament\Resources\JournalBatches\Pages\ListJournalBatches;
use App\Filament\Resources\JournalBatches\RelationManagers\JournalEntriesRelationManager;
use App\Models\JournalBatch;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JournalBatchResource extends Resource
{
    protected static ?string $model = JournalBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $navigationLabel = 'Jurnal Umum';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('manual_journal_warning')
                    ->content('⚠️ **Manual Journal Entry**: Gunakan hanya untuk jurnal penyesuaian, koreksi, transaksi aset tetap, atau entri non-operasional. Jurnal expense/payment umumnya di-generate otomatis dari data transaksi.')
                    ->columnSpanFull()
                    ->visible(fn ($livewire) => $livewire instanceof CreateRecord),

                Section::make('Informasi Jurnal')
                    ->schema([
                        Select::make('manual_journal_type')
                            ->label('Jenis Jurnal Manual')
                            ->options([
                                'adjustment' => 'Jurnal Penyesuaian (Depreciation, Accruals, etc.)',
                                'correction' => 'Jurnal Koreksi (Error correction, Reclassification)',
                                'asset' => 'Jurnal Aset Tetap (Purchase, Disposal, etc.)',
                                'financial' => 'Jurnal Keuangan (Loan, Investment, Bank charges)',
                                'tax' => 'Jurnal Pajak (Tax provision, Tax payment)',
                                'other' => 'Lainnya (Specify in description)',
                            ])
                            ->helperText('Pilih kategori jurnal manual untuk membantu tracking dan audit')
                            ->visible(fn ($livewire) => $livewire instanceof CreateRecord)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('batch_number')
                                    ->label('Nomor Batch')
                                    ->helperText('Nomor unik untuk identifikasi batch jurnal. Otomatis di-generate sistem.')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => JournalBatch::generateBatchNumber())
                                    ->maxLength(20),

                                DatePicker::make('transaction_date')
                                    ->label('Tanggal Transaksi')
                                    ->helperText('Tanggal ketika transaksi terjadi. Tidak boleh tanggal masa depan.')
                                    ->required()
                                    ->default(now()),

                                Select::make('status')
                                    ->label('Status')
                                    ->helperText('Draft: Dapat diedit. Posted: Sudah final. Reversed: Dibatalkan.')
                                    ->options([
                                        'draft' => 'Draft',
                                        'posted' => 'Posted',
                                        'reversed' => 'Reversed',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),

                        Textarea::make('description')
                            ->label('Keterangan')
                            ->helperText('Deskripsi transaksi jurnal. Contoh: "Pembelian equipment untuk acara Wedding Sari" atau "Pembayaran vendor catering"')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('reference_type')
                                    ->label('Jenis Referensi')
                                    ->helperText('Jenis dokumen yang menjadi dasar jurnal. Contoh: "expense", "payment", "revenue", "adjustment"')
                                    ->maxLength(255)
                                    ->placeholder('Contoh: expense, payment, revenue'),

                                TextInput::make('reference_id')
                                    ->label('ID Referensi')
                                    ->helperText('ID dari dokumen referensi (expense ID, payment ID, order ID, dll)')
                                    ->numeric()
                                    ->placeholder('Contoh: 144, 1052, 2031'),
                            ]),
                    ]),

                Section::make('Total Transaksi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('total_debit')
                                    ->label('Total Debit')
                                    ->helperText('Total nilai debit dalam jurnal. Dihitung otomatis dari journal entries.')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->default(0.00)
                                    ->readOnly(),

                                TextInput::make('total_credit')
                                    ->label('Total Kredit')
                                    ->helperText('Total nilai kredit dalam jurnal. Harus sama dengan total debit.')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->default(0.00)
                                    ->readOnly(),
                            ]),
                    ]),

                Section::make('Approval')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('created_by')
                                    ->label('Dibuat Oleh')
                                    ->relationship('createdBy', 'name')
                                    ->default(1) // Default to user ID 1 for now
                                    ->required()
                                    ->disabled(),

                                Select::make('approved_by')
                                    ->label('Disetujui Oleh')
                                    ->relationship('approvedBy', 'name')
                                    ->nullable(),

                                DateTimePicker::make('approved_at')
                                    ->label('Tanggal Persetujuan')
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('transaction_date', 'desc')
            ->columns([
                TextColumn::make('batch_number')
                    ->label('Nomor Batch')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('total_debit')
                    ->label('Total Debit')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('total_credit')
                    ->label('Total Kredit')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft' => 'warning',
                        'posted' => 'success',
                        'reversed' => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'draft' => 'Draft',
                        'posted' => 'Posted',
                        'reversed' => 'Reversed',
                        default => $state
                    }),

                TextColumn::make('reference_type')
                    ->label('Jenis Referensi')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(),

                TextColumn::make('approvedBy.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Status Jurnal')
                    ->placeholder('Hanya Aktif')
                    ->trueLabel('Hanya Terhapus')
                    ->falseLabel('Dengan Terhapus'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'posted' => 'Posted',
                        'reversed' => 'Reversed',
                    ]),

                Filter::make('transaction_date')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    Action::make('post_journal')
                        ->label('Post Jurnal')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (JournalBatch $record) {
                            if ($record->isBalanced()) {
                                $record->update(['status' => 'posted']);

                                Notification::make()
                                    ->title('Jurnal Berhasil Di-Post')
                                    ->body("Batch {$record->batch_number} telah di-post")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Jurnal Tidak Seimbang')
                                    ->body('Debit dan Kredit harus seimbang untuk posting')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->visible(fn (JournalBatch $record) => $record->status === 'draft'),

                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Buat Jurnal Pertama')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('reference_type', 'NOT LIKE', '%_reversal');
    }

    public static function getRelations(): array
    {
        return [
            JournalEntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalBatches::route('/'),
            'create' => CreateJournalBatch::route('/create'),
            'edit' => EditJournalBatch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total batch jurnal';
    }
}
