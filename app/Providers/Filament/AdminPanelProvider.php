<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\MenuItem;
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

            // ✅ Branding
            ->brandName('Moyo Safi Tailoring Center')
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div style="display: flex; align-items: center; gap: 12px; padding: 4px 0;">
                    <img src="' . asset('images/logo.png') . '" alt="Logo" style="height: 3rem; width: auto; object-fit: contain;" />
                    <span style="font-weight: 700; font-size: 1.1rem; line-height: 1.2; text-align: left;">
                        Moyo Safi<br>
                        <span style="font-size: 0.8rem; font-weight: 500; opacity: 0.8;">Tailoring Center</span>
                    </span>
                </div>
            '))
            ->brandLogoHeight('auto')

            // ✅ Put "Welcome Victor" in the TOP BAR (near logo)
            ->userMenuItems([
                'welcome' => MenuItem::make()
                    ->label(fn () => 'Welcome ' . (auth()->user()->name ?? ''))
                    ->icon('heroicon-o-user-circle'),
            ])

            // ✅ Theme colors
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
            ])
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Student Management')
                    ->icon('heroicon-o-users'),
                    
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Academics')
                    ->icon('heroicon-o-book-open'),
                    
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            // ✅ REMOVE AccountWidget so it doesn't show as a big card on Dashboard
            ->widgets([
                // (leave empty or remove this block completely)
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
