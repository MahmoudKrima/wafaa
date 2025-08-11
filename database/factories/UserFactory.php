<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\City;

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
        $adminId = fake()->randomElement([1, 2]);
        return [
            'name' => [
                'ar' => fake()->word(),
                'en' => fake()->word(),
            ],
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->regexify('05[0-9]{8}'),
            'additional_phone' => fake()->unique()->regexify('05[0-9]{8}'),
            'email_verified_at' => now(),
            'password' => '123456789',
            'city_id' => City::inRandomOrder()->value('id'),
            'created_by' => $adminId,
            'added_by' => $adminId,
            'address' => fake()->address(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set the created_by to a specific admin ID
     */
    public function createdBy(int $adminId): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => $adminId,
        ]);
    }

    /**
     * Set the city to a specific city ID
     */
    public function inCity(int $cityId): static
    {
        return $this->state(fn(array $attributes) => [
            'city_id' => $cityId,
        ]);
    }
}
