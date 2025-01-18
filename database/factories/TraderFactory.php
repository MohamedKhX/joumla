<?php

namespace Database\Factories;

use App\Enums\StoreTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trader>
 */
class TraderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_name' => $this->faker->name(),
            'phone'      => $this->faker->phoneNumber(),
            'city'       => $this->faker->city(),
            'address'    => $this->faker->address(),
            'trader_type_id' => $this->faker->randomElement([1,2,3,4,5,6,7,8,9,10])
        ];
    }
}
