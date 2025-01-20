<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            // Food Products
            ['name' => 'زيت زيتون طرابلسي', 'category' => 'مواد غذائية', 'price' => [15, 25]],
            ['name' => 'تمر ليبي فاخر', 'category' => 'مواد غذائية', 'price' => [8, 15]],
            ['name' => 'كسكسي منزلي', 'category' => 'مواد غذائية', 'price' => [5, 12]],
            ['name' => 'بزار ليبي', 'category' => 'مواد غذائية', 'price' => [3, 8]],

            // Cleaning Products
            ['name' => 'صابون طرابلسي تقليدي', 'category' => 'مواد تنظيف', 'price' => [2, 5]],
            ['name' => 'منظف أرضيات ليبي', 'category' => 'مواد تنظيف', 'price' => [4, 10]],

            // Traditional Items
            ['name' => 'شاي ليبي ممتاز', 'category' => 'مواد غذائية', 'price' => [10, 20]],
            ['name' => 'عسل ليبي طبيعي', 'category' => 'مواد غذائية', 'price' => [30, 50]],

            // Building Materials
            ['name' => 'إسمنت ليبي', 'category' => 'مواد بناء', 'price' => [30, 45]],
            ['name' => 'حديد تسليح محلي', 'category' => 'مواد بناء', 'price' => [100, 150]],
        ];

        $product = fake()->randomElement($products);
        $price = fake()->numberBetween($product['price'][0], $product['price'][1]);

        return [
            'name' => $product['name'],
            'description' => 'منتج ليبي عالي الجودة',
            'price' => $price,
        ];
    }
}
