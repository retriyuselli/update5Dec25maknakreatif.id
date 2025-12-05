<?php

namespace App\Enums;

enum StatusPiutang: string
{
    case AKTIF = 'aktif';
    case DIBAYAR_SEBAGIAN = 'dibayar_sebagian';
    case LUNAS = 'lunas';
    case JATUH_TEMPO = 'jatuh_tempo';
    case DIBATALKAN = 'dibatalkan';

    public function getLabel(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::DIBAYAR_SEBAGIAN => 'Dibayar Sebagian',
            self::LUNAS => 'Lunas',
            self::JATUH_TEMPO => 'Jatuh Tempo',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AKTIF => 'warning',
            self::DIBAYAR_SEBAGIAN => 'info',
            self::LUNAS => 'success',
            self::JATUH_TEMPO => 'danger',
            self::DIBATALKAN => 'gray',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
