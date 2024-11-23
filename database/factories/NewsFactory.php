<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => fake()->numberBetween(1, 10), // فرض بر این است که دسته‌ها بین 1 تا 10 هستند
            'title' => fake()->sentence(),
            'title_second' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'summary' => fake()->text(150),
            'body' => fake()->paragraphs(3, true),
            'image' => fake()->imageUrl(),
            'tags' => fake()->words(3, true), // تولید تگ‌ها به صورت رشته
            'status' => fake()->randomElement(['published', 'unpublished', 'trashed', 'archived']),
            'created_by' => fake()->numberBetween(1, 20), // فرض بر این است که کاربران بین 1 تا 20 هستند
            'updated_by' => fake()->numberBetween(1, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
