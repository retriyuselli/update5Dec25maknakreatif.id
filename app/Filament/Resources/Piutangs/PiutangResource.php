<?php

namespace App\Filament\Resources\Piutangs;

use App\Enums\JenisPiutang;
use App\Enums\StatusPiutang;
use App\Filament\Resources\Piutangs\Pages\CreatePiutang;
use App\Filament\Resources\Piutangs\Pages\EditPiutang;
use App\Filament\Resources\Piutangs\Pages\ListPiutangs;
use App\Filament\Resources\Piutangs\Pages\ViewPiutang;
use App\Filament\Resources\Piutangs\Widgets\PiutangJatuhTempoWidget;
use App\Filament\Resources\Piutangs\Widgets\PiutangOverviewWidget;
use App\Filament\Resources\Piutangs\Widgets\TopDebiturWidget;
use App\Models\Piutang;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Piutang';

    protected static ?string $modelLabel = 'Piutang';

    protected static ?string $pluralModelLabel = 'Piutang';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Piutang')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nomor_piutang')
                                    ->label('Nomor Piutang')
                                    ->default(fn () => Piutang::generateNomorPiutang())
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Select::make('jenis_piutang')
                                    ->label('Jenis Piutang')
                                    ->options(JenisPiutang::getOptions())
                                    ->required(),

                                Select::make('prioritas')
                                    ->label('Prioritas')
                                    ->options([
                                        'rendah' => 'Rendah',
                                        'sedang' => 'Sedang',
                                        'tinggi' => 'Tinggi',
                                        'mendesak' => 'Mendesak',
                                    ])
                                    ->default('sedang')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama_debitur')
                                    ->label('Nama Debitur')
                                    ->required()
                                    ->placeholder('Nama yang berhutang kepada kita'),

                                TextInput::make('kontak_debitur')
                                    ->label('Kontak Debitur')
                                    ->placeholder('No. HP/Telepon untuk follow up')
                                    ->tel(),
                            ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan Piutang')
                            ->required()
                            ->placeholder('Jelaskan detail piutang, invoice, dll')
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Keuangan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('jumlah_pokok')
                                    ->label('Jumlah Pokok')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $pokok = (float) $state;
                                        $bunga = (float) $get('persentase_bunga') ?? 0;
                                        $totalBunga = ($pokok * $bunga) / 100;
                                        $total = $pokok + $totalBunga;
                                        $set('total_piutang', $total);
                                        $set('sisa_piutang', $total);
                                    }),

                                TextInput::make('persentase_bunga')
                                    ->label('Bunga (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $pokok = (float) $get('jumlah_pokok') ?? 0;
                                        $bunga = (float) $state ?? 0;
                                        $totalBunga = ($pokok * $bunga) / 100;
                                        $total = $pokok + $totalBunga;
                                        $set('total_piutang', $total);
                                        $set('sisa_piutang', $total);
                                    }),

                                TextInput::make('total_piutang')
                                    ->label('Total Piutang')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ]),

                Section::make('Tanggal & Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('tanggal_piutang')
                                    ->label('Tanggal Piutang')
                                    ->required()
                                    ->default(now()),

                                DatePicker::make('tanggal_jatuh_tempo')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->required()
                                    ->minDate(now()),

                                Select::make('status')
                                    ->label('Status')
                                    ->options(StatusPiutang::getOptions())
                                    ->default('aktif')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Lampiran & Catatan')
                    ->schema([
                        FileUpload::make('lampiran')
                            ->label('Lampiran')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(2048)
                            ->helperText('Upload dokumen pendukung (PDF, gambar). Maksimal 2MB per file.'),

                        Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->placeholder('Catatan atau informasi tambahan tentang piutang ini'),
                    ]),

                Hidden::make('dibuat_oleh')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_piutang')
                    ->label('Nomor Piutang')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('jenis_piutang')
                    ->label('Jenis')
                    ->formatStateUsing(fn ($state) => $state instanceof JenisPiutang ? $state->getLabel() : JenisPiutang::from($state)->getLabel())
                    ->badge()
                    ->color(fn ($state) => match ($state instanceof JenisPiutang ? $state->value : $state) {
                        'operasional' => 'warning',
                        'pribadi' => 'danger',
                        'bisnis' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('nama_debitur')
                    ->label('Debitur')
                    ->searchable()
                    ->limit(20),

                TextColumn::make('kontak_debitur')
                    ->label('Kontak')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_piutang')
                    ->label('Total Piutang')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('sudah_dibayar')
                    ->label('Sudah Dibayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('sisa_piutang')
                    ->label('Sisa Piutang')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state instanceof StatusPiutang ? $state->getLabel() : StatusPiutang::from($state)->getLabel())
                    ->badge()
                    ->color(fn ($state) => $state instanceof StatusPiutang ? $state->getColor() : StatusPiutang::from($state)->getColor()),

                TextColumn::make('prioritas')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'rendah' => 'gray',
                        'sedang' => 'info',
                        'tinggi' => 'warning',
                        'mendesak' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('jenis_piutang')
                    ->label('Jenis Piutang')
                    ->options(JenisPiutang::getOptions()),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusPiutang::getOptions()),

                Filter::make('jatuh_tempo')
                    ->label('Akan Jatuh Tempo')
                    ->query(fn (Builder $query): Builder => $query->akanJatuhTempo(7)),

                Filter::make('sudah_jatuh_tempo')
                    ->label('Sudah Jatuh Tempo')
                    ->query(fn (Builder $query): Builder => $query->jatuhTempo()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('terima_pembayaran')
                    ->label('Terima Pembayaran')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    // ->url(fn (Piutang $record) => PembayaranPiutangResource::getUrl('create', ['piutang_id' => $record->id]))
                    ->visible(fn (Piutang $record) => in_array($record->status, [StatusPiutang::AKTIF, StatusPiutang::DIBAYAR_SEBAGIAN, StatusPiutang::JATUH_TEMPO])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_jatuh_tempo', 'asc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Piutang')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('nomor_piutang')
                                    ->label('Nomor Piutang')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('jenis_piutang')
                                    ->label('Jenis Piutang')
                                    ->formatStateUsing(fn ($state) => $state instanceof JenisPiutang ? $state->getLabel() : JenisPiutang::from($state)->getLabel())
                                    ->badge(),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->formatStateUsing(fn ($state) => $state instanceof StatusPiutang ? $state->getLabel() : StatusPiutang::from($state)->getLabel())
                                    ->badge()
                                    ->color(fn ($state) => $state instanceof StatusPiutang ? $state->getColor() : StatusPiutang::from($state)->getColor()),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama_debitur')
                                    ->label('Debitur'),

                                TextEntry::make('kontak_debitur')
                                    ->label('Kontak Debitur')
                                    ->visible(fn ($record) => $record->kontak_debitur),
                            ]),

                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Keuangan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('jumlah_pokok')
                                    ->label('Jumlah Pokok')
                                    ->money('IDR'),

                                TextEntry::make('persentase_bunga')
                                    ->label('Bunga')
                                    ->suffix('%'),

                                TextEntry::make('total_piutang')
                                    ->label('Total Piutang')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('sudah_dibayar')
                                    ->label('Sudah Dibayar')
                                    ->money('IDR')
                                    ->color('success'),

                                TextEntry::make('sisa_piutang')
                                    ->label('Sisa Piutang')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold)
                                    ->color('danger'),
                            ]),
                    ]),

                Section::make('Tanggal')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tanggal_piutang')
                                    ->label('Tanggal Piutang')
                                    ->date('d M Y'),

                                TextEntry::make('tanggal_jatuh_tempo')
                                    ->label('Jatuh Tempo')
                                    ->date('d M Y'),

                                TextEntry::make('tanggal_lunas')
                                    ->label('Tanggal Lunas')
                                    ->date('d M Y')
                                    ->visible(fn ($record) => $record->tanggal_lunas),
                            ]),
                    ]),

                Section::make('Catatan & Lampiran')
                    ->schema([
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->visible(fn ($record) => $record->catatan),

                        TextEntry::make('lampiran')
                            ->label('Lampiran')
                            ->visible(fn ($record) => $record->lampiran),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiutangs::route('/'),
            'create' => CreatePiutang::route('/create'),
            'view' => ViewPiutang::route('/{record}'),
            'edit' => EditPiutang::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'aktif')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getWidgets(): array
    {
        return [
            PiutangOverviewWidget::class,
            PiutangJatuhTempoWidget::class,
            TopDebiturWidget::class,
        ];
    }
}
