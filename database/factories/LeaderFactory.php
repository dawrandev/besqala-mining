<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Leader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Leader>
 */
class LeaderFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->name();
        $position = fake()->jobTitle();

        return [
            'full_name' => ['uz' => $name, 'ru' => $name, 'en' => $name],
            'position' => ['uz' => $position, 'ru' => $position, 'en' => $position],
            'department' => ['uz' => fake()->company(), 'ru' => fake()->company(), 'en' => fake()->company()],
            'bio' => ['uz' => fake()->paragraph(), 'ru' => fake()->paragraph(), 'en' => fake()->paragraph()],
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
