<?php

namespace App\Filament\Resources\SimulasiProduks\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Resources\SimulasiProduks\SimulasiProdukResource;
use App\Models\SimulasiProduk;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimulasiProduk extends EditRecord
{
    protected static string $resource = SimulasiProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('penawaran')
                ->label('Preview')
                ->color('success')
                ->icon('heroicon-o-eye')
                ->url(fn (SimulasiProduk $record) => route('simulasi.show', $record))
                ->openUrlInNewTab(),
        ];
    }
}
