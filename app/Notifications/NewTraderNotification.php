<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Trader;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewTraderNotification extends Notification
{
    use Queueable;

    public function __construct(public Trader $trader)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make()
            ->title('هناك محل تجزئة جديد قد اشترك معنا')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->body("قم بمراجعة تفاصيله وتفعيله")
            ->getDatabaseMessage();

    }
}
