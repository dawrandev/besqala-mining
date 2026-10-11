<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Inspection;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inspection>
 */
class InspectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'act_number' => 'D-2026-'.fake()->unique()->numerify('####'),
            'resident_id' => Resident::factory(),
            'subject' => fake()->randomElement([
                'Elektr energiyasini hisobga olish tizimi',
                'Ruxsat berish talablari va shartlariga rioya etilishi',
                'Ekologik normalarga muvofiqlik',
            ]),
            'status' => 'completed',
            'result' => 'clean',
            'has_violation' => false,
            'findings' => fake()->sentence(),
            'conducted_at' => now()->subDays(fake()->numberBetween(1, 40)),
        ];
    }

    public function withViolation(): static
    {
        return $this->state(fn () => ['result' => 'violation', 'has_violation' => true]);
    }
}
