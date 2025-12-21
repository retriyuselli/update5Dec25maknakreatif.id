<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum DocumentCategoryType: string implements HasLabel, HasColor
{
    case INTERNAL = 'internal';
    case OUTBOUND = 'outbound';
    case INBOUND = 'inbound';
    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INTERNAL => 'Internal (SK, Memo, SOP, etc.)',
            self::OUTBOUND => 'Outbound (Surat Keluar)',
            self::INBOUND => 'Inbound (Surat Masuk)',
            self::OTHER => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INTERNAL => 'info',
            self::OUTBOUND => 'warning',
            self::INBOUND => 'success',
            self::OTHER => 'gray',
        };
    }
}
