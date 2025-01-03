<?php

namespace App\Models;

use App\Enums\OrderStateEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'date',
        'state',
        'trader_id',
        'wholesale_store_id',
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
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }
}
