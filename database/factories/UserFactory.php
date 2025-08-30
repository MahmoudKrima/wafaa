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
            'password' => '123456789',
            'created_by' => $adminId,
            'added_by' => $adminId,
            'remember_token' => Str::random(10),
        ];
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
}
