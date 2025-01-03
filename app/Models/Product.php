<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $guarded = [];

    public function wholesaleStore(): BelongsTo
    {
        return $this->belongsTo(WholesaleStore::class);
    }

    public function thumbnail(): Attribute
    {
        return Attribute::get(function () {
            return $this->getMedia('thumbnails')->first()->getUrl();
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnails')
            ->performOnCollections('thumbnails')
            ->width(368)
            ->height(232);
    }
}
