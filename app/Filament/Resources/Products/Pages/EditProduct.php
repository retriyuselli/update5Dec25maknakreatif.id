<?php

namespace App\Filament\Resources\Products\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('penawaran')
                ->label('Preview')
                ->color('success')
                ->icon('heroicon-o-eye')
                ->url(fn (Product $record): string => route('products.details', ['product' => $record, 'action' => 'preview'])) // <-- Use 'products.details'
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
