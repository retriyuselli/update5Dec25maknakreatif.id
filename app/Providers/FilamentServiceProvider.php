<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //     Filament::serving(function () {
        //     Filament::navigationGroups([
        //         NavigationGroup::make()
        //             ->label('Hak Akses') // Ganti dari "Filament Shield" ke nama yang kamu inginkan
        //             ->icon('heroicon-o-shield-check'), // opsional
        //             // Anda bisa atur icon atau urutan juga
        //     ]);
        // });
    }
}
