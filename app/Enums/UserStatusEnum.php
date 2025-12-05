<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserStatusEnum: string implements HasLabel
{
    // Definisikan kasus-kasus Enum baru Anda.
    // Pastikan nilai string setelah '=' adalah unik dan sesuai dengan yang ingin Anda simpan di database.
    case KARYAWAN = 'karyawan';
    case ACCOUNT_MANAGER = 'account_manager';
    case EVENT_MANAGER = 'event_manager';
    case FINANCE = 'finance';
    case FREELANCE = 'freelance';
    case VENDOR = 'vendor';
    case MEDIA_SOSIAL_SPECIALIST = 'media_sosial_specialist';
    case ADMIN_AM_EM = 'admin_am_em';

    /**
     * Metode ini akan memberikan label yang lebih user-friendly untuk ditampilkan di UI Filament.
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::KARYAWAN => 'Karyawan',
            self::ACCOUNT_MANAGER => 'Account Manager',
            self::EVENT_MANAGER => 'Event Manager',
            self::FINANCE => 'Finance',
            self::FREELANCE => 'Freelance',
            self::VENDOR => 'Vendor',
            self::MEDIA_SOSIAL_SPECIALIST => 'Media Sosial Specialist',
            self::ADMIN_AM_EM => 'Admin AM & Em',
        };
    }

    /**
     * Opsional: Anda bisa menambahkan metode lain, misalnya untuk mendapatkan warna badge di Filament.
     * Sesuaikan warna sesuai preferensi Anda.
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::KARYAWAN => 'primary', // Warna biru
            self::ACCOUNT_MANAGER => 'success', // Warna hijau
            self::EVENT_MANAGER => 'info', // Warna cyan
            self::FINANCE => 'warning', // Warna kuning
            self::FREELANCE => 'secondary', // Warna abu-abu
            self::VENDOR => 'danger', // Warna merah
            self::MEDIA_SOSIAL_SPECIALIST => 'purple', // Contoh warna kustom
            self::ADMIN_AM_EM => 'gray', // Warna abu-abu tua
        };
    }
}
