<?php

namespace Database\Factories;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => [
                'en' => fake()->name(),
                'ar' => fake()->name(),
            ],
            'job_title' => [
                'en' => fake()->jobTitle(),
                'ar' => fake()->jobTitle(),
            ],
            'review' => [
                'en' => fake()->paragraph(),
                'ar' => fake()->paragraph(),
            ],
            'rate' => fake()->numberBetween(1, 5),
            'status' => fake()->randomElement(ActivationStatusEnum::vals()),
        ];
    }
}
