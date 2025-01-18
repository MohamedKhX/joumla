<?php

namespace App\Models;

use App\Enums\StoreTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Trader extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $guarded = [];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function licenseUrl(): ?string
    {
        return $this->getMedia('license')->first()?->getUrl();
    }

    public function traderType(): BelongsTo
    {
        return $this->belongsTo(TraderType::class);
    }

    public function registerMediaCollections():void
    {
        $this->addMediaCollection('logo')
            ->singleFile();

        $this->addMediaCollection('license')
            ->singleFile();
    }
}
