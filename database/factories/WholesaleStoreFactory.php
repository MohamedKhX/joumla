<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WholesaleStore>
 */
class WholesaleStoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $libyanCities = [
            'طرابلس', 'بنغازي', 'مصراتة', 'الزاوية', 'زليتن', 'صبراتة', 'غريان',
            'البيضاء', 'طبرق', 'سرت', 'الخمس', 'درنة', 'سبها'
        ];

        $commercialAreas = [
            'السوق القديم', 'شارع عمر المختار', 'شارع الجمهورية', 'المدينة القديمة',
            'السوق الجديد', 'المنطقة الصناعية', 'شارع الفاتح', 'شارع النصر'
        ];

        return [
            'name' => 'شركة ' . fake()->randomElement([
                'الأمان', 'النور', 'الصفاء', 'البركة', 'الوفاء', 'السلام', 'الخير',
                'الرحمة', 'الهدى', 'الفجر', 'النهضة', 'التقوى', 'الإيمان', 'المستقبل'
            ]) . ' للتجارة',
            'city' => fake()->randomElement($libyanCities),
            'address' => fake()->randomElement($commercialAreas) . '، ' . fake()->buildingNumber(),
            'phone' => fake()->e164PhoneNumber(),
            'wholesale_store_type_id' => fake()->numberBetween(1, 10)
        ];
    }
}
