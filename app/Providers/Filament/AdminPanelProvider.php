<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Models\Company;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\RedirectUnauthenticatedToAppUrl;
use Filament\Support\Enums\Width;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $company = null;
        if (Schema::hasTable('companies')) {
            $company = Company::query()->first();
        }
        $brandLogo = $company && $company->logo_url
            ? Storage::disk('public')->url($company->logo_url)
            : asset('images/logomki.png');
        $favicon = $company && $company->favicon_url
            ? Storage::disk('public')->url($company->favicon_url)
            : asset('images/favicon_makna.png');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->font('Noto Sans')
            ->login()
            ->maxContentWidth(Width::Full)
            ->brandLogo($brandLogo)
            ->brandLogoHeight('2rem')
            ->brandName('Makna Kreatif')
            ->favicon($favicon)
            ->sidebarCollapsibleOnDesktop(true)
            ->colors([
                'primary' => Color::Purple,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->renderHook('panels::body.end', fn () => view('filament.inactivity-redirect'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                RedirectUnauthenticatedToAppUrl::class,
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationLabel('Role')
                    ->navigationGroup('SDM'),
            ]);
    }
}
