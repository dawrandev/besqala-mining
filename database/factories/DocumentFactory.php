<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $kind = fake()->randomElement(DocumentKind::cases());

        return [
            'number' => $kind->numberPrefix().'-2026-'.fake()->unique()->numerify('####'),
            'kind' => $kind,
            'status' => DocumentStatus::Active,
            'resident_id' => Resident::factory(),
            'payload' => [],
            'template_version' => 'v1',
            'issued_at' => now()->subMonths(fake()->numberBetween(1, 10)),
            'expires_at' => now()->addYears(5),
        ];
    }

    public function certificate(): static
    {
        return $this->state(fn () => [
            'kind' => DocumentKind::Certificate,
            'number' => 'G-2026-'.fake()->unique()->numerify('####'),
        ]);
    }
}
