<?php

namespace App\Enums;

enum TransactionCategoryUangMasuk: string
{
    case uang_diterima = 'Uang Diterima';
    case penjualan = 'Penjualan';
    case piutang_dibayar = 'Piutang Dibayar';
    case pendanaan = 'Pendanaan';
    case bunga_bank = 'Bunga Bank';
    case pengembalian_biaya = 'Pengembalian Biaya';
    case pendapatan_lain = 'Pendapatan Lain';

    public function label(): string
    {
        return match ($this) {
            self::uang_diterima => 'Uang Diterima',
            self::penjualan => 'Penjualan',
            self::piutang_dibayar => 'Pembayaran Piutang',
            self::pendanaan => 'Pendanaan',
            self::bunga_bank => 'Bunga Bank',
            self::pengembalian_biaya => 'Pengembalian Biaya',
            self::pendapatan_lain => 'Pendapatan Lain',
        };
    }
}
