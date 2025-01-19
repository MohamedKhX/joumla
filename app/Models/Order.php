<?php

namespace App\Models;

use App\Contracts\HasUniqueNumberInterface;
use App\Enums\OrderStateEnum;
use App\Traits\HasUniqueNumber;
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
                        
                    case OrderStateEnum::DriverAccepted:
                        $order->trader->user->notify(new DriverAcceptedOrderNotification($order));
                        break;
                        
                    case OrderStateEnum::PickedUp:
                        $order->trader->user->notify(new OrderPickedUpNotification($order));
                        break;
                        
                    case OrderStateEnum::Delivered:
                        $order->trader->user->notify(new OrderDeliveredNotification($order));
                        $order->wholesaleStore->user->notify(new OrderDeliveredNotification($order));
                        break;
                }
            }
        });
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class, 'shipment_order');
    }

    public function getNumberPrefix(): string
    {
        return 'ORD-';
    }
}
