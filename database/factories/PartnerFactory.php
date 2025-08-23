<?php

namespace Database\Factories;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'defaults/admin.jpg',
            'status' => fake()->randomElement(ActivationStatusEnum::vals()),
        ];
    }
}
