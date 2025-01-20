<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WholesaleStoreType>
 */
class WholesaleStoreTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'مواد غذائية',
                'مواد بناء',
                'أدوات منزلية',
                'ملابس',
                'إلكترونيات',
                'مواد تنظيف',
                'عطور وتجميل',
                'أثاث منزلي',
                'قطع غيار سيارات',
                'أدوات مكتبية',
            ]),
        ];
    }
}
