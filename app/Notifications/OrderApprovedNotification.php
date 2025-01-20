<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class OrderApprovedNotification extends Notification
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
            ->title('تم قبول الطلب')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->body("تم قبول طلبك من قبل {$this->order->wholesaleStore->name}")
            ->getDatabaseMessage();

    }
}
