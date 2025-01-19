<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DriverAcceptedOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'قبول السائق للطلب',
            'message' => "تم قبول طلبك من قبل السائق {$this->order->shipment->driver->name}",
            'order_id' => $this->order->id,
            'type' => 'driver_accepted',
        ]);
    }
} 