<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ExpertVerdict;
use App\Models\Application;
use App\Models\ExpertConclusion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpertConclusion>
 */
class ExpertConclusionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'verdict' => ExpertVerdict::Positive,
            'comment' => fake()->sentence(),
            'members' => ['R. Nurmuxamedov (rais)', 'D. Li', 'S. Allambergenova'],
            'concluded_at' => now()->subDays(fake()->numberBetween(1, 20)),
        ];
    }

    public function negative(): static
    {
        return $this->state(fn () => ['verdict' => ExpertVerdict::Negative]);
    }
}
