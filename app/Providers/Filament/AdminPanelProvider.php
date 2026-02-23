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
                <div style="display: flex; align-items: center; justify-content: center; gap: 12px; padding: 4px 0; width: 100%;">
                    <img src="' . asset('images/logo.png') . '" alt="Logo" style="height: ' . (str_contains(request()->path(), 'login') ? '6rem' : '3rem') . '; width: auto; object-fit: contain;" />
                    <span style="font-weight: 700; font-size: ' . (str_contains(request()->path(), 'login') ? '2rem' : '1.1rem') . '; line-height: 1.2; text-align: left;">
                        Moyo Safi<br>
                        <span style="font-size: ' . (str_contains(request()->path(), 'login') ? '1.2rem' : '0.8rem') . '; font-weight: 500; opacity: 0.8;">Tailoring Center</span>
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
                    ->label('Enrollment Management')
                    ->icon('heroicon-o-academic-cap'),
                    
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

            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Backup & Export Data')
                    ->url(fn () => route('system.backup'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->group('Settings')
                    ->sort(99)
                    ->visible(fn() => auth()->user()?->hasRole('admin') ?? false),

                \Filament\Navigation\NavigationItem::make('Log Out')
                    ->url(fn (): string => route('system.logout'))
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->group('Settings')
                    ->sort(100),
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
