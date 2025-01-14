<?php

namespace App\Traits;

use App\Contracts\HasUniqueNumberInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin Model
 * */
trait HasUniqueNumber
{
    protected static function bootHasUniqueNumber(): void
    {
        static::creating(function ($model) {
            if (empty($model->number)) {
                $model->number = $model->generateUniqueNumber();
            }
        });
    }

    public function generateUniqueNumber(): string
    {
        $prefix = $this instanceof HasUniqueNumberInterface ? $this->getNumberPrefix() : 'default-';

        $lastNumber = self::where('number', 'like', "$prefix%")->latest('id')->value('number');
        $nextNumber = $lastNumber ? intval(Str::after($lastNumber, $prefix)) + 1 : 1;

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
