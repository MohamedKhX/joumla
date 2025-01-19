<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Notifications extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'الإشعارات';
    protected static ?string $title = 'الإشعارات';

    protected static string $view = 'filament.pages.notifications';

    public function getViewData(): array
    {
        return [
            'notifications' => Auth::user()
                ->notifications()
                ->orderByDesc('created_at')
                ->paginate(10),
        ];
    }

    public function markAsRead(string $notificationId): void
    {
        Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead(): void
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);
    }
} 