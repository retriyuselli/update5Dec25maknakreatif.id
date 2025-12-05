<?php

namespace App\Enums;

enum JenisPiutang: string
{
    case OPERASIONAL = 'operasional';
    case PRIBADI = 'pribadi';
    case BISNIS = 'bisnis';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPERASIONAL => 'Piutang Operasional',
            self::PRIBADI => 'Piutang Pribadi',
            self::BISNIS => 'Piutang Bisnis',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
