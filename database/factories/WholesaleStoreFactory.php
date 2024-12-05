<?php

namespace Database\Factories;

use App\Enums\WholesaleStoreEnum;
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
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),

            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
            'type' => $this->faker->randomElement(WholesaleStoreEnum::values())
        ];
    }
}
