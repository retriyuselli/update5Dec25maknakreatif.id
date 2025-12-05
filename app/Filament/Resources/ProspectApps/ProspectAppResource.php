<?php

namespace App\Filament\Resources\ProspectApps;

use App\Filament\Resources\ProspectApps\Pages\CreateProspectApp;
use App\Filament\Resources\ProspectApps\Pages\EditProspectApp;
use App\Filament\Resources\ProspectApps\Pages\ListProspectApps;
use App\Filament\Resources\ProspectApps\Pages\ViewProspectApp;
use App\Models\ProspectApp;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProspectAppResource extends Resource
{
    protected static ?string $model = ProspectApp::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $navigationLabel = 'Aplikasi Prospek';

    protected static ?string $modelLabel = 'Aplikasi Prospek';

    protected static ?string $pluralModelLabel = 'Aplikasi Prospek';

    protected static string|\UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kontak')
                    ->description('Masukkan detail kontak pelamar')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('contoh: John Doe')
                            ->autofocus(),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('contoh: john.doe@example.com'),

                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder('contoh: +6281234567890')
                            ->prefix('+62'),

                        TextInput::make('position')
                            ->label('Posisi Pekerjaan')
                            ->maxLength(255)
                            ->placeholder('contoh: Manajer Marketing')
                            ->helperText('Opsional'),
                    ])
                    ->columns(2),

                Section::make('Informasi Perusahaan')
                    ->description('Masukkan detail perusahaan pelamar')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('contoh: Acme Corp'),

                        Select::make('industry_id')
                            ->label('Industri')
                            ->relationship('industry', 'industry_name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih industri')
                            ->helperText('Pilih industri yang paling sesuai'),

                        TextInput::make('name_of_website')
                            ->label('Website/Domain')
                            ->maxLength(255)
                            ->placeholder('contoh: www.example.com')
                            ->url()
                            ->helperText('Opsional'),

                        Select::make('user_size')
                            ->label('Ukuran Perusahaan')
                            ->options([
                                '11-50' => '11-50 karyawan',
                                '51-200' => '51-200 karyawan',
                                '201-500' => '201-500 karyawan',
                                '501-1000' => '501-1000 karyawan',
                                '1000+' => '1000+ karyawan',
                            ])
                            ->placeholder('Pilih ukuran perusahaan')
                            ->helperText('Perkiraan jumlah karyawan'),
                    ])
                    ->columns(2),

                Section::make('Detail Aplikasi')
                    ->description('Detail aplikasi dan layanan yang diinginkan')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Textarea::make('reason_for_interest')
                            ->label('Alasan Ketertarikan')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Jelaskan alasan Anda tertarik pada layanan kami'),

                        Select::make('status')
                            ->label('Status Aplikasi')
                            ->options([
                                'pending' => 'Menunggu Tinjauan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('pending')
                            ->required(),

                        Select::make('service')
                            ->label('Paket Layanan')
                            ->options([
                                'basic' => 'Paket Basic - Segera Hadir',
                                'standard' => 'Paket Standar - Rp 8.500.000',
                                'premium' => 'Paket Premium - Segera Hadir',
                                'enterprise' => 'Paket Enterprise - Segera Hadir',
                            ])
                            ->placeholder('Pilih paket layanan')
                            ->helperText('Pilih paket yang paling sesuai kebutuhan Anda')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-update harga and bayar based on selected service
                                match ($state) {
                                    'standard' => [
                                        $set('harga', 8500000),
                                        $set('bayar', 8500000),
                                    ],
                                    'basic', 'premium', 'enterprise' => [
                                        $set('harga', null),
                                        $set('bayar', null),
                                    ],
                                    default => [
                                        $set('harga', null),
                                        $set('bayar', null),
                                    ],
                                };
                            }),

                        TextInput::make('harga')
                            ->label('Perkiraan Anggaran')
                            ->numeric()
                            ->prefix('Rp ')
                            ->placeholder('Harga akan otomatis sesuai paket')
                            ->helperText('Anggaran otomatis terisi saat memilih paket')
                            ->dehydrated()
                            ->readOnly(),

                        DatePicker::make('tgl_bayar')
                            ->label('Tanggal Pembayaran')
                            ->displayFormat('d M Y')
                            ->helperText('Jika ada pembayaran, isi tanggalnya'),

                        TextInput::make('bayar')
                            ->label('Jumlah Dibayar')
                            ->numeric()
                            ->prefix('Rp ')
                            ->helperText('Jika ada pembayaran, isi nominalnya')
                            ->dehydrated(),

                        RichEditor::make('notes')
                            ->label('Catatan Internal')
                            ->placeholder('Tambahkan catatan internal atau komentar'),

                        DateTimePicker::make('submitted_at')
                            ->label('Tanggal & Waktu Pengajuan')
                            ->default(now())
                            ->displayFormat('d M Y H:i'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('industry.industry_name')
                    ->label('Industri')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('position')
                    ->label('Posisi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),

                TextColumn::make('service')
                    ->label('Paket Layanan')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('harga')
                    ->label('Anggaran')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bayar')
                    ->label('Jumlah Dibayar')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tgl_bayar')
                    ->label('Tanggal Pembayaran')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user_size')
                    ->label('Ukuran Perusahaan')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('submitted_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->placeholder('All Statuses'),

                SelectFilter::make('industry')
                    ->relationship('industry', 'industry_name')
                    ->searchable()
                    ->preload()
                    ->placeholder('All Industries'),

                SelectFilter::make('user_size')
                    ->label('Company Size')
                    ->options([
                        '1-10' => '1-10 employees',
                        '11-50' => '11-50 employees',
                        '51-200' => '51-200 employees',
                        '201-500' => '201-500 employees',
                        '501-1000' => '501-1000 employees',
                        '1000+' => '1000+ employees',
                    ])
                    ->placeholder('All Sizes'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),

                    EditAction::make()
                        ->color('warning'),

                    Action::make('generateProposal')
                        ->label('Generate Proposal')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->url(fn (ProspectApp $record): string => route('prospect-app.proposal.pdf', $record))
                        ->openUrlInNewTab(),

                    DeleteAction::make(),
                ])
                    ->label('Actions')
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
            ->defaultSort('submitted_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
            'index' => ListProspectApps::route('/'),
            'create' => CreateProspectApp::route('/create'),
            'view' => ViewProspectApp::route('/{record}'),
            'edit' => EditProspectApp::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total aplikasi prospek';
    }
}
