<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Company')
                    ->tabs([
                        Tabs\Tab::make('Informasi Perusahaan')
                            ->icon(Heroicon::OutlinedPencilSquare)
                            ->schema([
                                Section::make('Informasi Perusahaan')
                                    ->schema([
                                        Grid::make()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('company_name')
                                                    ->required()
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->placeholder('Nama perusahaan'),
                                                TextInput::make('business_license')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Nomor izin usaha'),
                                                TextInput::make('owner_name')
                                                    ->required()
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->placeholder('Nama pemilik'),
                                                TextInput::make('legal_entity_type')
                                                    ->maxLength(100)
                                                    ->placeholder('PT, CV, Firma'),
                                                TextInput::make('established_year')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue((int) date('Y'))
                                                    ->placeholder('Tahun berdiri'),
                                                TextInput::make('employee_count')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100000)
                                                    ->default(fn () => User::count())
                                                    ->formatStateUsing(fn ($state) => User::count())
                                                    ->disabled()
                                                    ->placeholder('Jumlah karyawan'),
                                            ]),
                                    ]),
                                Section::make('Informasi Rekening')
                                    ->schema([
                                        Select::make('payment_method_id')
                                            ->label('Rekening Bank Utama')
                                            ->relationship('paymentMethod', 'bank_name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->bank_name} - {$record->no_rekening} ({$record->name})")
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Pilih rekening bank utama'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Kontak & Alamat')
                            ->icon(Heroicon::OutlinedCurrencyDollar)
                            ->schema([
                                Section::make('Kontak & Alamat')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('email')
                                                    ->label('Email address')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255)
                                                    ->placeholder('contoh@perusahaan.com'),
                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->required()
                                                    ->minLength(8)
                                                    ->maxLength(20)
                                                    ->regex('/^[0-9+\s\-]+$/')
                                                    ->placeholder('+62 812 xxxx xxxx'),
                                            ]),
                                        Textarea::make('address')
                                            ->required()
                                            ->maxLength(1000)
                                            ->columnSpanFull()
                                            ->placeholder('Alamat lengkap perusahaan'),
                                        Grid::make()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('city')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->placeholder('Kota/Kabupaten'),
                                                TextInput::make('province')
                                                    ->required()
                                                    ->maxLength(100)
                                                    ->placeholder('Provinsi'),
                                                TextInput::make('postal_code')
                                                    ->required()
                                                    ->minLength(4)
                                                    ->maxLength(10)
                                                    ->regex('/^[0-9]+$/')
                                                    ->placeholder('Kode pos'),
                                            ]),
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('website')
                                                    ->url()
                                                    ->maxLength(255)
                                                    ->placeholder('https://example.com')
                                                    ->helperText('Gunakan URL lengkap diawali http:// atau https://'),
                                                FileUpload::make('logo_url')
                                                    ->disk('public')
                                                    ->directory('company/logo')
                                                    ->image()
                                                    ->maxSize(5120),
                                            ]),
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                FileUpload::make('favicon_url')
                                                    ->disk('public')
                                                    ->directory('company/favicon')
                                                    ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml'])
                                                    ->imagePreviewHeight('64')
                                                    ->maxSize(5120),
                                                FileUpload::make('image_login')
                                                    ->label('Image Login')
                                                    ->disk('public')
                                                    ->directory('company/login_image')
                                                    ->image()
                                                    ->maxSize(5120)
                                                    ->helperText('Gambar untuk halaman login frontend'),
                                                TextInput::make('logo_url')
                                                    ->hidden(),
                                            ]),
                                        Textarea::make('description')
                                            ->maxLength(1000)
                                            ->columnSpanFull()
                                            ->placeholder('Deskripsi singkat perusahaan'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Legal Perusahaan')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->schema([
                                Section::make('Legal Perusahaan')
                                    ->schema([
                                        Grid::make()
                                            ->columns(3)
                                            ->schema([
                                                TextInput::make('deed_of_establishment')
                                                    ->maxLength(255)
                                                    ->placeholder('Nomor akta pendirian'),
                                                DatePicker::make('deed_date'),
                                                TextInput::make('notary_name')
                                                    ->maxLength(255)
                                                    ->placeholder('Nama notaris'),
                                                TextInput::make('notary_license_number')
                                                    ->maxLength(100)
                                                    ->placeholder('Nomor izin notaris'),
                                                TextInput::make('nib_number')
                                                    ->maxLength(50)
                                                    ->placeholder('Nomor NIB'),
                                                Grid::make()
                                                    ->columns(2)
                                                    ->schema([
                                                        DatePicker::make('nib_issued_date'),
                                                        DatePicker::make('nib_valid_until'),
                                                    ]),
                                                TextInput::make('npwp_number')
                                                    ->maxLength(20)
                                                    ->regex('/^[0-9\.\-]+$/')
                                                    ->helperText('NPWP 15 digit, boleh berisi tanda titik atau minus'),
                                                DatePicker::make('npwp_issued_date'),
                                                TextInput::make('tax_office')
                                                    ->maxLength(255)
                                                    ->placeholder('Kantor pajak'),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Dokumen')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema([
                                Section::make('Dokumen')
                                    ->schema([
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                FileUpload::make('legal_documents')
                                                    ->disk('public')
                                                    ->directory('company/legal')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(5120)
                                                    ->helperText('Dokumen legal perusahaan (PDF)')
                                                    ->openable()
                                                    ->multiple()
                                                    ->dehydrateStateUsing(fn ($state) => $state ? (is_array($state) ? $state : [$state]) : []),
                                                Select::make('legal_document_status')
                                                    ->label('Status Legal Dokumen')
                                                    ->options([
                                                        'pending' => 'Belum diverifikasi',
                                                        'review' => 'Dalam review',
                                                        'verified' => 'Terverifikasi',
                                                        'expired' => 'Kedaluwarsa',
                                                        'rejected' => 'Ditolak',
                                                    ])
                                                    ->default('pending')
                                                    ->required()
                                                    ->native(false),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
