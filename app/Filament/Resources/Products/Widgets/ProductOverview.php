<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $approvedProducts = Product::where('is_approved', true)->count();

        $withVendors = Product::whereHas('items')->count();
        $withoutVendors = Product::whereDoesntHave('items')->count();
        $inOrders = Product::whereHas('orders')->count();

        return [
            Stat::make('Total Products', $totalProducts)
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make('Active', $activeProducts)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Approved', $approvedProducts)
                ->icon('heroicon-o-shield-check')
                ->color('success'),

            Stat::make('With Vendors', $withVendors)
                ->icon('heroicon-o-link')
                ->color('warning'),

            Stat::make('Without Vendors', $withoutVendors)
                ->icon('heroicon-o-minus-circle')
                ->color('gray'),

            Stat::make('In Orders', $inOrders)
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),
        ];
    }
}
