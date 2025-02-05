<?php

namespace App\Models;

use App\Enums\UserType;
use App\Enums\UserTypeEnum;
use App\Notifications\NewDriverNotification;
use App\Notifications\NewTraderNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia, FilamentUser
{
    use HasFactory,
        Notifiable,
        InteractsWithMedia,
        HasApiTokens,
        SoftDeletes;

    protected $guarded = [];

    protected $with = ['trader'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => UserTypeEnum::class
        ];
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if($user->type != UserTypeEnum::Driver) {
                return;
            }

            $admins = User::where('type', UserTypeEnum::Admin->value)->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewDriverNotification($user));
            }
        });
    }

    public function trader(): HasOne
    {
        return $this->hasOne(Trader::class);
    }

    public function wholesaleStore(): HasOne
    {
        return $this->hasOne(WholesaleStore::class);
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin' && $this->type == UserTypeEnum::Admin) {
            return true;
        }

        if ($panel->getId() === 'wholesaleStore' && $this->type == UserTypeEnum::Wholesaler) {
            return true;
        }

        return false;
    }
}
