<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusVendor: string implements HasLabel
{
    case VENDOR = 'vendor';
    case PRODUCT = 'product';
    case MASTER = 'master';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VENDOR => 'Vendor',
            self::PRODUCT => 'Product',
            self::MASTER => 'Master',
        };
    }
}
