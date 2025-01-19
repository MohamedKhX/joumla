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
        FilamentNotification::make()
            ->title('تم قبول الطلب')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->body("تم قبول طلبك من قبل {$this->order->wholesaleStore->name}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('عرض الطلب')
                    ->url(route('filament.trader.resources.orders.view', ['record' => $this->order->id]))
                    ->button(),
            ])
            ->getDatabaseMessage();

        return [
            'title' => 'تم قبول الطلب',
            'message' => "تم قبول طلبك من قبل {$this->order->wholesaleStore->name}",
            'order_id' => $this->order->id,
            'type' => 'order_approved',
        ];
    }
} 