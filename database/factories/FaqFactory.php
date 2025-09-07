<?php

namespace Database\Factories;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => fake()->sentence(),
            'answer' => fake()->paragraph(),
            'status' => fake()->randomElement(ActivationStatusEnum::vals()),
        ];
    }
}
