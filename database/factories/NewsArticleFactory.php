<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NewsArticle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsArticle>
 */
class NewsArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'title' => ['uz' => $title, 'ru' => $title, 'en' => $title],
            'excerpt' => ['uz' => fake()->sentence(), 'ru' => fake()->sentence(), 'en' => fake()->sentence()],
            'body' => ['uz' => fake()->paragraphs(3, true), 'ru' => fake()->paragraphs(3, true), 'en' => fake()->paragraphs(3, true)],
            'published_at' => now()->subDays(fake()->numberBetween(1, 60)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }
}
