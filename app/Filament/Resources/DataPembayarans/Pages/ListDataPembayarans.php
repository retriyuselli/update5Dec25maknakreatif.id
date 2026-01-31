<?php

namespace App\Filament\Resources\DataPembayarans\Pages;

use App\Filament\Resources\DataPembayarans\DataPembayaranResource;
use App\Filament\Resources\DataPembayarans\Widgets\DataPembayaranStatsOverview;
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
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('data-pembayaran.pdf-report'))
                ->color('success')
                ->openUrlInNewTab(),

            Action::make('viewHtmlReport')
                ->label('Laporan Pembayaran')
                ->icon('heroicon-o-document-text')
                ->url(route('data-pembayaran.html-report'))
                ->openUrlInNewTab()
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
