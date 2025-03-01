<?php

namespace App\Models;

use App\Enums\WholesaleStoreEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WholesaleStore extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia,
        SoftDeletes;

    protected $guarded = [];


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

    public function wholesaleStoreType(): BelongsTo
    {
        return $this->belongsTo(WholesaleStoreType::class);
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
