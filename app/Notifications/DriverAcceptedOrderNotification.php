<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Shipment;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DriverAcceptedOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Shipment $shipment)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make()
            ->title('قبول السائق للطلب')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->body("تم قبول طلبك من قبل السائق {$this->shipment->driver->name}")
            ->getDatabaseMessage();
    }
}
