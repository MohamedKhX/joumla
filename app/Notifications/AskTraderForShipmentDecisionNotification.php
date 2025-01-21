<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use App\Models\Shipment;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AskTraderForShipmentDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Shipment $shipment,
        public array $rejectedOrders
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // Get rejected stores
        $rejectedStores = collect($this->rejectedOrders)
            ->map(function($order) {
                $order = Order::find($order['id']);
                return $order->wholesaleStore->name;
            });

        // Get accepted orders
        $acceptedOrders = $this->shipment->orders()
            ->whereNotIn('id', collect($this->rejectedOrders)->pluck('id'))
            ->get();

        // Get accepted stores
        $acceptedStores = $acceptedOrders->map(function($order) {
            return $order->wholesaleStore->name;
        });

        // Prepare orders data for frontend
        $ordersData = collect()
            ->concat($acceptedStores->map(fn($store) => [
                'store_name' => $store,
                'state' => 'Approved'
            ]))
            ->concat($rejectedStores->map(fn($store) => [
                'store_name' => $store,
                'state' => 'Rejected'
            ]));

        return [
            'type' => 'trader_order_decision',
            'title' => 'تم رفض بعض الطلبات',
            'body' => "تم رفض طلباتك من {$rejectedStores->implode(' و ')}. هل تريد المتابعة مع الطلبات المقبولة؟",
            'orders' => $ordersData->toArray(),
            'shipment_id' => $this->shipment->id,
            'actions' => [
                'accept' => [
                    'label' => 'متابعة مع الطلبات المقبولة',
                    'url' => "/shipments/{$this->shipment->id}/proceed-with-approved"
                ],
                'cancel' => [
                    'label' => 'إلغاء جميع الطلبات',
                    'url' => "/shipments/{$this->shipment->id}/cancel-all"
                ]
            ]
        ];
    }
}
