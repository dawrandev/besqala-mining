<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationEvent>
 */
class ApplicationEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'type' => 'submitted',
            'title' => 'Ariza topshirildi',
            'note' => null,
        ];
    }
}
