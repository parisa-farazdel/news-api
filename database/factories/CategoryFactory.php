<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => fake()->numberBetween(0, 10), // فرض بر این است که دسته‌های والد بین 0 تا 10 هستند
            'title' => fake()->word(),
            'status' => fake()->randomElement(['published', 'unpublished', 'trashed', 'archived']),
            'created_by' => fake()->numberBetween(1, 20), // فرض بر این است که کاربران بین 1 تا 20 هستند
            'updated_by' => fake()->numberBetween(1, 10),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
