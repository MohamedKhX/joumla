<?php

namespace App\Models;

use App\Contracts\HasUniqueNumberInterface;
use App\Enums\ShipmentStateEnum;
use App\Traits\HasUniqueNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model implements HasUniqueNumberInterface
{
    use HasFactory,
        HasUniqueNumber;

    protected $guarded = [];

    protected $casts = [
        'state' => ShipmentStateEnum::class,
    ];

    protected $appends = [
        'total_amount',
        'totalAmount'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function total_amount(): Attribute
    {
        return Attribute::get(function() {
            return $this->orders->sum('totalAmount');
        });
    }

    public function totalAmount(): Attribute
    {
        return Attribute::get(function() {
            return $this->orders->sum('totalAmount');
        });
    }

    public function getNumberPrefix(): string
    {
        return 'shipment-';
    }
}
