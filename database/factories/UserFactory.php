<?php

namespace Database\Factories;

use App\Enums\UserTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $libyanFirstNames = [
            'محمد', 'أحمد', 'علي', 'عمر', 'خالد', 'إبراهيم', 'عبدالله', 'يوسف', 'حسين', 'مصطفى',
            'فاطمة', 'عائشة', 'مريم', 'زينب', 'خديجة', 'أسماء', 'نور', 'سارة', 'ليلى', 'هاجر'
        ];

        $libyanLastNames = [
            'القذافي', 'المصراتي', 'الزنتاني', 'البرعصي', 'الورفلي', 'التاجوري', 'الككلي',
            'العبيدي', 'الفيتوري', 'الزليتني', 'البنغازي', 'الطرابلسي', 'السرتي', 'الزاوي'
        ];

        $name = fake()->randomElement($libyanFirstNames) . ' ' . fake()->randomElement($libyanLastNames);

        return [
            'name' => $name,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'phone' => '+218' . fake()->numberBetween(91, 94) . fake()->numberBetween(1000000, 9999999),
            'type' => fake()->randomElement(UserTypeEnum::cases()),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
