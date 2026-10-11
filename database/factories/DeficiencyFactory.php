<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Application;
use App\Models\Deficiency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deficiency>
 */
class DeficiencyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'items' => ['Yangi mayning uskunasi hujjati yetishmaydi'],
            'status' => 'open',
        ];
    }

    public function fulfilled(): static
    {
        return $this->state(fn () => ['status' => 'fulfilled', 'resolved_at' => now()]);
    }
}
