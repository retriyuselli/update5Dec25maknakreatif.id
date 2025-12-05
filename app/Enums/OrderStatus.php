<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {

            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Done => 'Done',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-arrow-path',
            self::Processing => 'heroicon-m-truck',
            self::Done => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Done => 'primary',
            self::Processing => 'success',
            self::Cancelled => 'danger',
        };
    }
}
