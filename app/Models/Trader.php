<?php

namespace App\Models;

use App\Enums\StoreTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Trader extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'store_type' => StoreTypeEnum::class
    ];

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
}
