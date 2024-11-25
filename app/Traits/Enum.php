<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait Enum
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }


    public static function getTranslationKey(): string
    {
        return 'enums.' . Str::snake(class_basename(get_called_class()));
    }

    /**
     * Returns all translated enums of this class as value => translation
     */
    public static function getTranslations(): array
    {
        $translations = Lang::get(self::getTranslationKey());
        return (is_array($translations)) ? $translations : [];
    }

    /**
     * Returns all translated enums of this class as value => translation
     */
    public function translate(): string
    {
        return Lang::get(self::getTranslationKey() . '.' . $this->value);
    }
}
