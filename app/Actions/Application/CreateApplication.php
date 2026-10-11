<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateApplication
{
    /**
     * Create a new application for an applicant and log the opening event.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes, User $applicant, bool $submit = false): Application
    {
        return DB::transaction(function () use ($attributes, $applicant, $submit): Application {
            $status = $submit ? ApplicationStatus::Submitted : ApplicationStatus::Draft;

            $application = Application::query()->create([
                ...$attributes,
                'number' => $this->nextNumber(),
                'applicant_id' => $applicant->id,
                'status' => $status,
                'submitted_at' => $submit ? now() : null,
            ]);

            if ($submit) {
                ApplicationEvent::query()->create([
                    'application_id' => $application->id,
                    'actor_id' => $applicant->id,
                    'type' => 'submitted',
                    'title' => __('Ariza topshirildi'),
                ]);
            }

            return $application;
        });
    }

    /**
     * Generate the next sequential application number, e.g. "A-2026-0042".
     */
    private function nextNumber(): string
    {
        $year = now()->year;
        $prefix = sprintf('A-%d-', $year);

        $highest = Application::query()
            ->where('number', 'like', $prefix.'%')
            ->get(['number'])
            ->map(fn (Application $a): int => (int) substr((string) $a->number, strlen($prefix)))
            ->max() ?? 0;

        return sprintf('%s%04d', $prefix, $highest + 1);
    }
}
