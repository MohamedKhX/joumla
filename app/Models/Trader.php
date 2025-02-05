<?php

namespace App\Models;

use App\Enums\StoreTypeEnum;
use App\Enums\UserTypeEnum;
use App\Notifications\NewTraderNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Trader extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia,
        SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($trader) {
            $admins = User::where('type', UserTypeEnum::Admin->value)->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewTraderNotification($trader));
            }
        });
    }


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
