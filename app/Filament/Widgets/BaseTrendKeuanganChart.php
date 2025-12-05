<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;

abstract class BaseTrendKeuanganChart extends ChartWidget
{
    use HasWidgetShield;

    public ?int $tahun = null;

    protected int|string|array $columnSpan = 'full';

    protected function getFormSchema(): array
    {
        $currentYear = now()->year;
        $years = range($currentYear, $currentYear - 5); // Ambil 6 tahun termasuk tahun ini

        return [
            Select::make('tahun')
                ->label('Tahun')
                ->options(array_combine($years, $years))
                ->default($currentYear)
                ->live(), // Menggunakan live() untuk Filament v3, menggantikan reactive()
            // afterStateUpdated tidak diperlukan karena live() akan memicu refresh widget
            // ->afterStateUpdated(fn() => $this->updateChartData()),
        ];
    }
}
