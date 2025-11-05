<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);
        $status = $this->faker->randomElement(['draft', 'review', 'published']);
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'category_id' => $this->faker->boolean(70) ? Category::inRandomOrder()->value('id') : null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'excerpt' => $this->faker->optional()->paragraph(),
            'body' => $this->faker->paragraphs(6, true),
            'status' => $status,
            'published_at' => $status === 'published' ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
            'featured_image' => null,
            'reading_time' => $this->faker->numberBetween(2, 10),
        ];
    }
}
