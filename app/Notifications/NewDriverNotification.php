<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewDriverNotification extends Notification
{
    use Queueable;

    public function __construct(public User $trader)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make()
            ->title('هناك سائق جديد قد اشترك معنا')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->body("قم بمراجعة تفاصيله وتفعيله")
            ->getDatabaseMessage();

    }
}
