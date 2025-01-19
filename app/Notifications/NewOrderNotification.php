<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make()
            ->title('طلب جديد')
            ->icon('heroicon-o-shopping-cart')
            ->body("لديك طلب جديد معلق من {$this->order->trader->store_name}")
            ->getDatabaseMessage();
    }
}
