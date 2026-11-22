<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetPortalTenant;
use App\Models\Tenant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
            // White-label: nome e logo da organizadora atual (resolvida pelo subdominio).
            ->brandName(fn (): string => Tenant::current()?->nome ?? 'Certamme')
            ->brandLogo(fn () => Tenant::current()?->logoUrl())
            ->brandLogoHeight('2rem')
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => view('filament.concurso-banner')->render(),
            )
            ->navigationGroups([
                'Configuração do Concurso',
                'Inscrições',
                'Candidatos',
                'Provas & Gabarito',
                'Títulos',
                'Resultados & Recursos',
                'Locais & Alocação',
                'Sistema',
            ])
            ->plugin(\BezhanSalleh\FilamentShield\FilamentShieldPlugin::make())
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetPortalTenant::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
