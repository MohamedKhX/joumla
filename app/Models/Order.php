<?php

namespace App\Models;

use App\Contracts\HasUniqueNumberInterface;
use App\Enums\OrderStateEnum;
use App\Enums\ShipmentStateEnum;
use App\Notifications\AskTraderForShipmentDecisionNotification;
use App\Notifications\OrderRejectedNotification;
use App\Traits\HasUniqueNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderApprovedNotification;
use App\Notifications\DriverAcceptedOrderNotification;
use App\Notifications\OrderPickedUpNotification;
use App\Notifications\OrderDeliveredNotification;

class Order extends Model implements HasUniqueNumberInterface
{
    use HasFactory,
        HasUniqueNumber;

   protected $guarded = [];

   protected $appends = [
       'shipmentState'
   ];

    protected $casts = [
        'date' => 'date',
        'state' => OrderStateEnum::class,
    ];

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function wholesaleStore(): BelongsTo
    {
        return $this->belongsTo(WholesaleStore::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            //$order->wholesale_store_id = auth()->user()->wholesaleStore->id;
            $order->number = 'ORD-' . str_pad((Order::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        });

        // When order is created
        static::created(function ($order) {
            $order->wholesaleStore->user->notify(new NewOrderNotification($order));
        });

        // When order is updated
        static::updated(function ($order) {
            if ($order->wasChanged('state')) {
                switch ($order->state) {
                    case OrderStateEnum::Approved:
                        $order->trader->user->notify(new OrderApprovedNotification($order));
                        break;

                    case OrderStateEnum::Rejected:
                        $order->trader->user->notify(new OrderRejectedNotification($order));
                        break;
                }

                $order->checkIfWeCanCreateShipment();
            }
        });
    }

    public function checkIfWeCanCreateShipment(): void
    {
        $shipment = $this->shipment;
        $orders = $shipment->orders;

        // Check if all orders are rejected
        if ($orders->every(fn($order) => $order->state === OrderStateEnum::Rejected)) {
            return;
        }

        // Check if any order is not rejected or not approved
        if ($orders->contains(fn($order) => $order->state !== OrderStateEnum::Rejected && $order->state !== OrderStateEnum::Approved)) {
            return;
        }

        // If all orders are approved, create shipment
        if ($orders->every(fn($order) => $order->state === OrderStateEnum::Approved)) {
            $this->changeShipmentStateToWaitingForShipping();
            return;
        }

        // If some orders are approved and some are rejected, notify trader
        if ($orders->contains(fn($order) => $order->state === OrderStateEnum::Approved) &&
            $orders->contains(fn($order) => $order->state === OrderStateEnum::Rejected)) {
            $this->trader->user->notify(new AskTraderForShipmentDecisionNotification());
        }
    }

    protected function changeShipmentStateToWaitingForShipping(): void
    {
        $this->shipment->update([
            'state' => ShipmentStateEnum::WaitingForShipping,
        ]);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /*public function shipmentState(): Attribute
    {
        return Attribute::get(function () {
            return $this->shipment->state;
        });
    }*/

    public function getShipmentStateAttribute()
    {
        return $this->shipment->state;
    }

    public function getNumberPrefix(): string
    {
        return 'ORD-';
    }
}
