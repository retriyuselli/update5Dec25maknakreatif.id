<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MonthEnum: string implements HasLabel
{
    case Januari = 'Januari';
    case Februari = 'Februari';
    case Maret = 'Maret';
    case April = 'April';
    case Mei = 'Mei';
    case Juni = 'Juni';
    case Juli = 'Juli';
    case Agustus = 'Agustus';
    case September = 'September';
    case Oktober = 'Oktober';
    case November = 'November';
    case Desember = 'Desember';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
