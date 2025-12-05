<?php

namespace App\Filament\Resources\DataPembayarans\Pages;

use App\Filament\Resources\DataPembayarans\Widgets\DataPembayaranStatsOverview;
use App\Filament\Resources\DataPembayarans\DataPembayaranResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListDataPembayarans extends ListRecords
{
    protected static string $resource = DataPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('viewHtmlReport')
                ->label('Laporan Pembayaran')
                ->icon('heroicon-o-document-text')
                ->url(route('data-pembayaran.html-report'), true) // 'true' untuk membuka di tab baru
                ->color('info'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DataPembayaranStatsOverview::class,
        ];
    }
}
