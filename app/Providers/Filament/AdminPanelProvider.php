<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
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
            ->brandName('Andina S.R.L. | Auditoría IA')
            ->colors([
                'primary' => [
                    50 => '#f0f5fa',
                    100 => '#e2ecf5',
                    200 => '#c4daec',
                    300 => '#96bede',
                    400 => '#619ecb',
                    500 => '#3d83b5',
                    600 => '#2b6897',
                    700 => '#23537a',
                    800 => '#1d4362',
                    900 => '#102C57', // Deep Navy Corporativo
                    950 => '#0b1c36',
                ],
                'success' => [
                    50 => '#eefcf5',
                    100 => '#d6f7e7',
                    200 => '#b0eed0',
                    300 => '#7be0b1',
                    400 => '#42cb8d',
                    500 => '#00A86B', // Emerald Green Auditoría
                    600 => '#14905a',
                    700 => '#13724a',
                    800 => '#135a3c',
                    900 => '#114a33',
                    950 => '#042a1b',
                ],
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'info' => Color::Sky,
            ])
            ->navigationGroups([
                'Adquisiciones & Proveedores',
                'Almacenes & Kardex',
                'Auditoría & Inteligencia',
                'Seguridad & Sistema',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
