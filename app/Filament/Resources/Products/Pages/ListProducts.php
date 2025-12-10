<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Widgets\ProductOverview;
use App\Filament\Resources\SimulasiProduks\SimulasiProdukResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction; // Pastikan namespace ini benar jika digunakan
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Product') // Anda bisa menyesuaikan label jika perlu
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembuatan Produk Baru')
                ->modalDescription('Pastikan kembali data yang akan Anda isi sudah benar sebelum melanjutkan.')
                ->modalSubmitActionLabel('Lanjutkan')
                ->modalCancelActionLabel('Batal'),

            // Aksi 'penawaran' Anda yang sudah ada
            Action::make('penawaran')
                ->label('Penawaran')
                ->color('success')
                ->icon('heroicon-o-eye')
                ->url(SimulasiProdukResource::getUrl('create')) // Pastikan SimulasiProdukResource di-import atau gunakan FQCN
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductOverview::class,
        ];
    }
}
