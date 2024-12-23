<?php

namespace App\Models;

use App\Enums\WholesaleStoreEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WholesaleStore extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'type' => WholesaleStoreEnum::class
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(WholesaleStoreSubscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function licenseUrl(): ?string
    {
        return $this->getMedia('license')->first()?->getUrl();
    }

    public function registerMediaCollections():void
    {
        $this->addMediaCollection('logo')
            ->singleFile();

        $this->addMediaCollection('license')
            ->singleFile();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('end_date', '>=', now())
            ->where('status', 'active')
            ->exists();
    }
}
