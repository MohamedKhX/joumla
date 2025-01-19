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
        FilamentNotification::make()
            ->title('طلب جديد')
            ->icon('heroicon-o-shopping-cart')
            ->body("لديك طلب جديد معلق من {$this->order->trader->store_name}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('عرض الطلب')
                    ->url(route('filament.wholesale-store.resources.orders.view', ['record' => $this->order->id]))
                    ->button(),
            ])
            ->getDatabaseMessage();

        return [
            'title' => 'طلب جديد',
            'message' => "لديك طلب جديد معلق من {$this->order->trader->store_name}",
            'order_id' => $this->order->id,
            'type' => 'new_order',
        ];
    }
} 