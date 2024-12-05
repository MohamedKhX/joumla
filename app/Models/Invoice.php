<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function wholesaleStore(): BelongsTo
    {
        return $this->belongsTo(WholesaleStore::class);
    }
}
