<?php

namespace App\Models;

use App\Contracts\HasUniqueNumberInterface;
use App\Traits\HasUniqueNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model implements HasUniqueNumberInterface
{
    use HasFactory,
        HasUniqueNumber;

    protected $guarded = [];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getNumberPrefix(): string
    {
        return 'shipment-';
    }
}
