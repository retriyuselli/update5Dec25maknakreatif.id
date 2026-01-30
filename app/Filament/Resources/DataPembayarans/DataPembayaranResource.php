<?php

namespace App\Filament\Resources\DataPembayarans;

use App\Enums\OrderStatus;
use App\Filament\Resources\DataPembayarans\Pages\EditDataPembayaran;
use App\Filament\Resources\DataPembayarans\Pages\ListDataPembayarans;
use App\Filament\Resources\DataPembayarans\Widgets\DataPembayaranStatsOverview;
use App\Models\DataPembayaran;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

// use App\Filament\Widgets\DataPembayaranStatsOverview;

class DataPembayaranResource extends Resource
{
    protected static ?string $model = DataPembayaran::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $recordTitleAttribute = 'keterangan';

    protected static ?string $navigationLabel = 'Pendapatan Wedding';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'name')
                    ->searchable()
                    ->disabled()
                    ->preload()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $order = Order::find($state);
                            $set('nominal', $order?->sisa ?? 0);
                        }
                    }),

                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->disabled()
                    ->prefix('Pembayaran')
                    ->placeholder('1, 2, 3 dst'),

                TextInput::make('nominal')
                    ->label('Amount')
                    ->disabled()
                    ->readOnly()
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('Rp. ')
                    ->required()
                    ->minValue(0)
                    ->rules(['max:999999999'])
                    ->columnSpan(['md' => 1]),

                Select::make('kategori_transaksi')
                    ->options([
                        'uang_masuk' => 'Uang Masuk',
                        'uang_keluar' => 'Uang Keluar',
                    ])
                    ->default('uang_masuk')
                    ->disabled()
                    ->label('Tipe Transaksi')
                    ->required(),

                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->searchable()
                    ->disabled()
                    ->preload(),

                DatePicker::make('tgl_bayar')
                    ->label('Payment Date')
                    ->required()
                    ->disabled(),

                FileUpload::make('image')
                    ->label('Payment Proof')
                    ->disabled()
                    ->image()
                    ->maxSize(1280)
                    ->disk('public')
                    ->directory('payment-proofs/'.date('Y/m'))
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->helperText('Max 1MB. JPG or PNG only.'),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['paymentMethod', 'order']))
            ->columns([
                TextColumn::make('order.name')
                    ->label('Order Number')
                    ->searchable()
                    ->label('Project')
                    ->sortable()
                    ->copyable(),

                TextColumn::make('tgl_bayar')
                    ->label('Payment Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('paymentMethod.name')
                    ->label('Payment Method')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nominal')
                    ->label('Amount')
                    ->formatStateUsing(fn (string $state): string => 'Rp. '.number_format($state, 0, ',', '.'))
                    ->summarize([
                        Sum::make()
                            ->formatStateUsing(fn (string $state): string => 'Rp. '.number_format($state, 0, ',', '.')),
                    ])
                    ->sortable(),

                ImageColumn::make('image')
                    ->label('Payment Proof')
                    ->circular(false)
                    ->sortable()
                    ->square(),

                TextColumn::make('keterangan')
                    ->label('Description')
                    ->prefix('Pembayaran ')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
            ])
            ->defaultSort('tgl_bayar', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('order_status')
                    ->label('Order Status')
                    ->options(OrderStatus::class) // Menggunakan Enum OrderStatus
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('order', function (Builder $orderQuery) use ($data) {
                            $orderQuery->where('status', $data['value']);
                        });
                    }),
                SelectFilter::make('payment_method')
                    ->relationship('paymentMethod', 'name')
                    ->preload()
                    ->multiple()
                    ->label('Payment Method'),

                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_bayar', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tgl_bayar', '<=', $date),
                            );
                    }),
            ])
            ->filtersFormColumns(3)

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->visible(fn (?DataPembayaran $record): bool => $record && ! $record->trashed() && ! $record->order_id)
                        ->requiresConfirmation(),
                    RestoreAction::make()
                        ->visible(fn (?DataPembayaran $record): bool => $record && $record->trashed()),
                    ForceDeleteAction::make()
                        ->visible(fn (?DataPembayaran $record): bool => $record && $record->trashed())
                        ->requiresConfirmation(),
                ]),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('restricted_delete')
                        ->label('Hapus Terpilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $deletable = $records->filter(fn ($r) => ! $r->order_id && ! $r->trashed());
                            $skipped = $records->count() - $deletable->count();

                            $deleted = 0;
                            foreach ($deletable as $rec) {
                                $rec->delete();
                                $deleted++;
                            }

                            if ($deleted > 0) {
                                Notification::make()
                                    ->success()
                                    ->title('Hapus selesai')
                                    ->body("Berhasil menghapus {$deleted} data.")
                                    ->send();
                            }

                            if ($skipped > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Sebagian dilewati')
                                    ->body("{$skipped} data terhubung ke Order dan tidak bisa dihapus")
                                    ->send();
                            }
                        }),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
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

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Order' => $record->order?->name,
            'Amount' => 'Rp. '.number_format($record->nominal, 0, ',', '.'),
            'Date' => $record->tgl_bayar ? Carbon::parse($record->tgl_bayar)->format('d M Y') : '-',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataPembayarans::route('/'),
            'edit' => EditDataPembayaran::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            DataPembayaranStatsOverview::class,
            // Anda juga bisa menambahkan LatestDataPembayaranTableWidget::class di sini jika diinginkan
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['keterangan', 'order.number', 'paymentMethod.name'];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<DataPembayaran> $model */
        $model = static::getModel();

        return cache()->remember('data_pembayaran_count', now()->addMinutes(5), function () use ($model) {
            return $model::query()
                ->whereNull('deleted_at')
                ->count();
        });
    }

    public static function getNavigationBadgeColor(): ?string
    {
        /** @var class-string<DataPembayaran> $model */
        $model = static::getModel();

        $count = cache()->remember('data_pembayaran_count', now()->addMinutes(5), function () use ($model) {
            return $model::query()
                ->whereNull('deleted_at')
                ->count();
        });

        return match (true) {
            $count > 10 => 'warning',
            $count > 0 => 'primary',
            default => 'secondary',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pembayaran dari konsumen ke perusahaan sebagai DP dan pembayaran lanjutan';
    }
}
