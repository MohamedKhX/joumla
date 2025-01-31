<?php

namespace App\Filament\WholesaleStore\Pages;

use Filament\Pages\Page;

class SubscriptionExpired extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $title = 'انتهى الاشتراك';
    protected static ?string $navigationLabel = 'انتهى الاشتراك';
    protected static ?string $slug = 'subscription-expired';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.wholesale-store.pages.subscription-expired';

    public function mount(): void
    {
        if (auth()->user()->wholesaleStore->hasActiveSubscription()) {
            $this->redirect('/wholesaleStore');
        }
    }

























    public static function getSlug(): string
    {
        return static::$slug ?? 'subscription-expired';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'lastSubscription' => auth()->user()->wholesaleStore->subscriptions()->latest()->first(),
        ];
    }
}
