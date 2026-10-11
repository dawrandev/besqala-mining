<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Region;
use App\Enums\ResidentStatus;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resident>
 */
class ResidentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => '«'.fake()->company().'» MChJ',
            'stir' => (string) fake()->unique()->numerify('3########'),
            'region' => fake()->randomElement(Region::cases()),
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'status' => ResidentStatus::Active,
            'address' => 'Qoraqalpog‘iston Respublikasi, '.fake()->streetAddress(),
            'location_address' => fake()->streetAddress(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('+998 61 ### ## ##'),
            'founders' => [
                ['name' => fake()->name(), 'share' => 60],
                ['name' => '«'.fake()->company().'» MChJ', 'share' => 40],
            ],
            'power_mw' => fake()->randomFloat(2, 1, 50),
            'energy_source' => fake()->randomElement(['Quyosh', 'Gidro', 'Shamol', 'Aralash']),
            'mined_total_btc' => fake()->randomFloat(4, 0, 20),
            'registered_at' => now()->subMonths(fake()->numberBetween(1, 12)),
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => ResidentStatus::Suspended]);
    }
}
