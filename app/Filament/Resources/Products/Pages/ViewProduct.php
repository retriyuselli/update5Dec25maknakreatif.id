<?php

namespace App\Filament\Resources\Products\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('penawaran')
                ->label('Preview')
                ->color('success')
                ->icon('heroicon-o-eye')
                ->url(fn (Product $record): string => route('products.details', ['product' => $record, 'action' => 'preview'])) // <-- Use 'products.details'
                ->openUrlInNewTab(),
        ];
    }
}
