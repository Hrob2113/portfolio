<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('HROB / CMS')
            ->darkMode(true, isForced: true)
            ->colors([
                // Primary — portfolio teal #1A5060 (readable, not harsh)
                'primary' => [
                    50 => '236, 248, 251',
                    100 => '207, 238, 245',
                    200 => '160, 218, 234',
                    300 => '101, 190, 215',
                    400 => '52, 158, 190',
                    500 => '30, 128, 160',
                    600 => '26, 100, 128',
                    700 => '22, 78, 100',
                    800 => '16, 58, 76',
                    900 => '10, 40, 52',
                    950 => '5, 22, 30',
                ],
                // Gray — warm dark scale matching #06060A background
                'gray' => [
                    50 => '237, 230, 218',
                    100 => '212, 204, 192',
                    200 => '176, 168, 157',
                    300 => '130, 122, 112',
                    400 => '84, 78, 70',
                    500 => '52, 48, 43',
                    600 => '32, 29, 26',
                    700 => '20, 18, 16',
                    800 => '13, 12, 10',
                    900 => '9, 9, 12',
                    950 => '6, 6, 10',
                ],
            ])
            ->font('IBM Plex Mono')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                StatsOverviewWidget::class,
            ])
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
                Authenticate::class,
            ]);
    }
}
