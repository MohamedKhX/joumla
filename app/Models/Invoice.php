<?php

namespace App\Models;

use App\Traits\HasUniqueNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory,
        HasUniqueNumber,
        SoftDeletes;

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function items(): Attribute
    {
        return Attribute::get(fn() => $this->order->items);
    }

    public function total_amount(): Attribute
    {
        return Attribute::get(fn() => $this->order->totalAmount);
    }

    public function totalAmount(): Attribute
    {
        return Attribute::get(fn() => $this->order->totalAmount);
    }

    public function wholesaleStore(): BelongsTo
    {
        return $this->belongsTo(WholesaleStore::class);
    }
}
