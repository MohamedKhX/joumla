<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WholesaleStoreSubscription extends Model
{
    /** @use HasFactory<\Database\Factories\WholesaleStoreSubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'wholesale_store_id',
        'start_date',
        'end_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function wholesaleStore(): BelongsTo
    {
        return $this->belongsTo(WholesaleStore::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if ($subscription->end_date < now()) {
                $subscription->status = 'expired';
            }
        });

        static::updating(function ($subscription) {
            if ($subscription->end_date < now()) {
                $subscription->status = 'expired';
            }
        });
    }
}
