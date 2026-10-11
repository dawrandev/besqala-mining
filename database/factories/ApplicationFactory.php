<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\Region;
use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => 'A-2026-'.fake()->unique()->numerify('####'),
            'applicant_id' => User::factory(),
            'type' => ApplicationType::Registration,
            'status' => ApplicationStatus::Draft,
            'company_name' => '«'.fake()->company().'» MChJ',
            'stir' => (string) fake()->numerify('3########'),
            'region' => fake()->randomElement(Region::cases()),
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'address' => fake()->streetAddress(),
            'location_address' => fake()->streetAddress(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('+998 61 ### ## ##'),
            'founders' => [['name' => fake()->name(), 'share' => 100]],
            'equipment' => [
                ['type' => 'ASIC', 'model' => 'Antminer S21', 'count' => fake()->numberBetween(50, 500), 'power_kw' => 3.5],
            ],
            'power_mw' => fake()->randomFloat(2, 1, 50),
            'energy_source' => fake()->randomElement(['Quyosh', 'Gidro', 'Shamol']),
            'bank_account' => fake()->numerify('########################'),
        ];
    }

    public function status(ApplicationStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'submitted_at' => $status === ApplicationStatus::Draft ? null : now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
