<?php

namespace App\Filament\Resources\ProspectApps\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;

class ProspectAppForm
{
    public static function configure(Schema $schema): Schema
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
                            ->placeholder('contoh: Manajer Marketing'),
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
                            ->placeholder('Pilih industri'),

                        TextInput::make('name_of_website')
                            ->label('Website/Domain')
                            ->maxLength(255)
                            ->placeholder('contoh: www.example.com'),

                        Select::make('user_size')
                            ->label('Ukuran Perusahaan')
                            ->options([
                                '1-10' => '1-10 karyawan',
                                '11-50' => '11-50 karyawan',
                                '50+' => '50+ karyawan',
                            ])
                            ->placeholder('Pilih ukuran perusahaan'),
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
                                'premium' => 'Paket Premium - Rp 15.000.000',
                                'enterprise' => 'Paket Enterprise - Rp 30.000.000',
                            ])
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                $mapping = [
                                    'standard' => 8500000,
                                    'premium' => 15000000,
                                    'enterprise' => 30000000,
                                    'basic' => null,
                                ];
                                $set('harga', $mapping[$state] ?? null);
                            })
                            ->helperText('Pilih paket layanan untuk mengisi anggaran otomatis'),
                    ])
                    ->columns(1),

                Section::make('Pembayaran & Catatan')
                    ->schema([
                        TextInput::make('harga')
                            ->label('Anggaran')
                            ->numeric()
                            ->prefix('Rp ')
                            ->dehydrated()
                            ->readOnly()
                            ->helperText('Anggaran otomatis terisi saat memilih paket'),

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
                            ->placeholder('Tambahkan catatan internal atau komentar')
                            ->columnSpanFull(),

                        DateTimePicker::make('submitted_at')
                            ->label('Tanggal & Waktu Pengajuan')
                            ->default(now())
                            ->displayFormat('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
