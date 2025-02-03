<?php

namespace App\Providers\Filament;

use App\Filament\WholesaleStore\Pages\SubscriptionExpired;
use App\Filament\WholesaleStore\Reports;
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
use App\Http\Middleware\CheckWholesaleStoreSubscription;
use Filament\Navigation\MenuItem;

class WholesaleStorePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('wholesaleStore')
            ->path('wholesaleStore')
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/WholesaleStore/Resources'), for: 'App\\Filament\\WholesaleStore\\Resources')
            ->discoverPages(in: app_path('Filament/WholesaleStore/Pages'), for: 'App\\Filament\\WholesaleStore\\Pages')
            ->pages([
                Reports::class,
                SubscriptionExpired::class,
            ])
            ->discoverWidgets(in: app_path('Filament/WholesaleStore/Widgets'), for: 'App\\Filament\\WholesaleStore\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                CheckWholesaleStoreSubscription::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->colors([
                'primary'   => Color::Blue,
                'gray'      => Color::Gray,
                'green'     => Color::Green,
                'red'       => Color::Red,
                'yellow'    => Color::Yellow,
                'blue'      => Color::Blue,
                'orange'    => Color::Orange,
                'darkgreen' => '#28a745',
                'purple'    => Color::Purple,
                'teal'      => Color::Teal,
            ])
            ->font('Rubik')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications();
    }
}
