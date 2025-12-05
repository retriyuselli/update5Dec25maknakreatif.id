<?php

namespace App\Filament\Resources\PembayaranPiutangs;

use App\Filament\Resources\PembayaranPiutangs\Pages\CreatePembayaranPiutang;
use App\Filament\Resources\PembayaranPiutangs\Pages\EditPembayaranPiutang;
use App\Filament\Resources\PembayaranPiutangs\Pages\ListPembayaranPiutangs;
use App\Filament\Resources\PembayaranPiutangs\Pages\ViewPembayaranPiutang;
use App\Models\PembayaranPiutang;
use App\Models\Piutang;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PembayaranPiutangResource extends Resource
{
    protected static ?string $model = PembayaranPiutang::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pembayaran Piutang';

    protected static ?string $modelLabel = 'Pembayaran Piutang';

    protected static ?string $pluralModelLabel = 'Pembayaran Piutang';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('piutang_id')
                                    ->label('Piutang')
                                    ->relationship('piutang', 'nomor_piutang', function (Builder $query) {
                                        return $query->whereIn('status', ['aktif', 'dibayar_sebagian', 'jatuh_tempo']);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $piutang = Piutang::find($state);
                                            $set('max_pembayaran', $piutang->sisa_piutang);
                                        }
                                    }),

                                TextInput::make('nomor_pembayaran')
                                    ->label('Nomor Pembayaran')
                                    ->default(fn () => PembayaranPiutang::generateNomorPembayaran())
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ]),

                        Grid::make(1)
                            ->schema([
                                Placeholder::make('info_piutang')
                                    ->label('Informasi Piutang')
                                    ->content(function (Get $get) {
                                        $piutangId = $get('piutang_id');
                                        if (! $piutangId) {
                                            return 'Pilih piutang terlebih dahulu';
                                        }

                                        $piutang = Piutang::find($piutangId);

                                        return "
                                            Debitur: {$piutang->nama_debitur}
                                            Total Piutang: Rp ".number_format($piutang->total_piutang, 0, ',', '.').'
                                            Sudah Dibayar: Rp '.number_format($piutang->sudah_dibayar, 0, ',', '.').'
                                            Sisa Piutang: Rp '.number_format($piutang->sisa_piutang, 0, ',', '.').'
                                        ';
                                    })
                                    ->visible(fn (Get $get) => $get('piutang_id')),
                            ]),
                    ]),

                Section::make('Detail Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('jumlah_pembayaran')
                                    ->label('Jumlah Pembayaran')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $pembayaran = (float) $state;
                                        $bunga = (float) $get('jumlah_bunga') ?? 0;
                                        $denda = (float) $get('denda') ?? 0;
                                        $set('total_pembayaran', $pembayaran + $bunga + $denda);
                                    }),

                                TextInput::make('jumlah_bunga')
                                    ->label('Bunga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $pembayaran = (float) $get('jumlah_pembayaran') ?? 0;
                                        $bunga = (float) $state ?? 0;
                                        $denda = (float) $get('denda') ?? 0;
                                        $set('total_pembayaran', $pembayaran + $bunga + $denda);
                                    }),

                                TextInput::make('denda')
                                    ->label('Denda')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $pembayaran = (float) $get('jumlah_pembayaran') ?? 0;
                                        $bunga = (float) $get('jumlah_bunga') ?? 0;
                                        $denda = (float) $state ?? 0;
                                        $set('total_pembayaran', $pembayaran + $bunga + $denda);
                                    }),
                            ]),

                        TextInput::make('total_pembayaran')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),
                    ]),

                Section::make('Metode & Tanggal')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('payment_method_id')
                                    ->label('Metode Pembayaran')
                                    ->relationship('paymentMethod', 'name')
                                    ->required(),

                                DatePicker::make('tanggal_pembayaran')
                                    ->label('Tanggal Pembayaran')
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('tanggal_dicatat')
                                    ->label('Tanggal Dicatat')
                                    ->default(now())
                                    ->required(),
                            ]),
                    ]),

                Section::make('Referensi & Konfirmasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nomor_referensi')
                                    ->label('Nomor Referensi')
                                    ->placeholder('Nomor referensi bank/transfer'),

                                Select::make('dikonfirmasi_oleh')
                                    ->label('Dikonfirmasi Oleh')
                                    ->relationship('dikonfirmasiOleh', 'name')
                                    ->default(Auth::id()),
                            ]),
                    ]),

                Section::make('Status & Catatan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'dikonfirmasi' => 'Dikonfirmasi',
                                        'dibatalkan' => 'Dibatalkan',
                                    ])
                                    ->default('pending')
                                    ->required(),

                                FileUpload::make('bukti_pembayaran')
                                    ->label('Bukti Pembayaran')
                                    ->multiple()
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(2048),
                            ]),

                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan pembayaran')
                            ->columnSpanFull(),
                    ]),

                Hidden::make('dibayar_oleh')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_pembayaran')
                    ->label('Nomor Pembayaran')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('piutang.nomor_piutang')
                    ->label('Nomor Piutang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('piutang.nama_debitur')
                    ->label('Debitur')
                    ->searchable()
                    ->limit(20),

                TextColumn::make('total_pembayaran')
                    ->label('Total Pembayaran')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('paymentMethod.name')
                    ->label('Metode'),

                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'dikonfirmasi' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('dikonfirmasiOleh.name')
                    ->label('Dikonfirmasi Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'dibatalkan' => 'Dibatalkan',
                    ]),

                SelectFilter::make('payment_method_id')
                    ->label('Metode Pembayaran')
                    ->relationship('paymentMethod', 'name'),

                Filter::make('tanggal_pembayaran')
                    ->schema([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pembayaran', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pembayaran', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (PembayaranPiutang $record) {
                        $record->update([
                            'status' => 'dikonfirmasi',
                            'dikonfirmasi_oleh' => Auth::id(),
                        ]);
                    })
                    ->visible(fn (PembayaranPiutang $record) => $record->status === 'pending'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('nomor_pembayaran')
                                    ->label('Nomor Pembayaran')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('piutang.nomor_piutang')
                                    ->label('Nomor Piutang')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'pending' => 'warning',
                                        'dikonfirmasi' => 'success',
                                        'dibatalkan' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),

                        TextEntry::make('piutang.nama_debitur')
                            ->label('Debitur'),

                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->catatan),
                    ]),

                Section::make('Detail Keuangan')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('jumlah_pembayaran')
                                    ->label('Jumlah Pembayaran')
                                    ->money('IDR'),

                                TextEntry::make('jumlah_bunga')
                                    ->label('Bunga')
                                    ->money('IDR'),

                                TextEntry::make('denda')
                                    ->label('Denda')
                                    ->money('IDR'),

                                TextEntry::make('total_pembayaran')
                                    ->label('Total Pembayaran')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold),
                            ]),
                    ]),

                Section::make('Informasi Piutang')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('piutang.total_piutang')
                                    ->label('Total Piutang')
                                    ->money('IDR'),

                                TextEntry::make('piutang.sudah_dibayar')
                                    ->label('Sudah Dibayar')
                                    ->money('IDR')
                                    ->color('success'),

                                TextEntry::make('piutang.sisa_piutang')
                                    ->label('Sisa Piutang')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold)
                                    ->color('danger'),
                            ]),
                    ]),

                Section::make('Metode & Tanggal')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('paymentMethod.name')
                                    ->label('Metode Pembayaran'),

                                TextEntry::make('tanggal_pembayaran')
                                    ->label('Tanggal Pembayaran')
                                    ->date('d M Y'),

                                TextEntry::make('tanggal_dicatat')
                                    ->label('Tanggal Dicatat')
                                    ->date('d M Y'),
                            ]),
                    ]),

                Section::make('Referensi & Konfirmasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nomor_referensi')
                                    ->label('Nomor Referensi')
                                    ->visible(fn ($record) => $record->nomor_referensi),

                                TextEntry::make('dikonfirmasiOleh.name')
                                    ->label('Dikonfirmasi Oleh')
                                    ->visible(fn ($record) => $record->dikonfirmasi_oleh),
                            ]),
                    ]),

                Section::make('Lampiran')
                    ->schema([
                        TextEntry::make('bukti_pembayaran')
                            ->label('Bukti Pembayaran')
                            ->visible(fn ($record) => $record->bukti_pembayaran),
                    ])
                    ->visible(fn ($record) => $record->bukti_pembayaran),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPembayaranPiutangs::route('/'),
            'create' => CreatePembayaranPiutang::route('/create'),
            'view' => ViewPembayaranPiutang::route('/{record}'),
            'edit' => EditPembayaranPiutang::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
