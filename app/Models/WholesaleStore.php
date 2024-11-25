<?php

namespace App\Models;

use App\Enums\WholesaleStoreEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WholesaleStore extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => WholesaleStoreEnum::class
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
