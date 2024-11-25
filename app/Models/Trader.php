<?php

namespace App\Models;

use App\Enums\StoreTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trader extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'store_type' => StoreTypeEnum::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
