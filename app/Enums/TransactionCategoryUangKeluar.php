<?php

namespace App\Enums;

enum TransactionCategoryUangKeluar: string
{
    case vendor_event = 'Vendor Event';
    case gaji_honorarium = 'Gaji Honorarium';
    case transportasi_akomodasi = 'Transportasi Akomodasi';
    case komunikasi_internet = 'Komunikasi Internet';
    case peralatan_perlengkapan_kantor = 'Peralatan Perlengkapan Kantor';
    case biaya_layanan_eksternal = 'Biaya Layanan Eksternal';
    case pemasaran_promosi = 'Pemasaran Promosi';
    case sewa_tempat_aset = 'Sewa Tempat Aset';
    case listrik_air_utilitas = 'Listrik Air Utilitas';
    case biaya_pelatihan_pengembangan = 'Biaya Pelatihan Pengembangan';
    case penyusutan_aset = 'Penyusutan Aset';
    case konsumsi_kebutuhan_rapat = 'Konsumsi Kebutuhan Rapat';
    case administrasi_bank_lainnya = 'Administrasi Bank ainnya';
    case pengeluaran_lain = 'Pengeluaran Lain';

    public function label(): string
    {
        return match ($this) {
            self::vendor_event => 'Vendor Event',
            self::gaji_honorarium => 'Gaji & Honorarium',
            self::transportasi_akomodasi => 'Transportasi & Akomodasi',
            self::komunikasi_internet => 'Komunikasi & Internet',
            self::peralatan_perlengkapan_kantor => 'Peralatan & Perlengkapan Kantor',
            self::biaya_layanan_eksternal => 'Biaya Layanan Eksternal',
            self::pemasaran_promosi => 'Pemasaran & Promosi',
            self::sewa_tempat_aset => 'Sewa Tempat atau Aset',
            self::listrik_air_utilitas => 'Listrik, Air & Utilitas',
            self::biaya_pelatihan_pengembangan => 'Biaya Pelatihan & Pengembangan',
            self::penyusutan_aset => 'Penyusutan Aset',
            self::konsumsi_kebutuhan_rapat => 'Konsumsi & Kebutuhan Rapat',
            self::administrasi_bank_lainnya => 'Administrasi Bank & Lainnya',
            self::pengeluaran_lain => 'Pengeluaran Lain',
        };
    }
}
