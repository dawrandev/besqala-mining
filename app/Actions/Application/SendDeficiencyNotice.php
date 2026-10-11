<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Deficiency;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SendDeficiencyNotice
{
    public function __construct(private readonly TransitionApplication $transition) {}

    /**
     * Record a deficiency notice and move the application to the deficiency state.
     *
     * @param  list<string>  $items
     */
    public function handle(Application $application, User $actor, array $items): Application
    {
        return DB::transaction(function () use ($application, $actor, $items): Application {
            Deficiency::query()->create([
                'application_id' => $application->id,
                'issued_by' => $actor->id,
                'items' => array_values(array_filter($items, static fn (string $i): bool => trim($i) !== '')),
                'status' => 'open',
            ]);

            return $this->transition->handle(
                $application,
                ApplicationStatus::Deficiency,
                $actor,
                __('Kamchilik xabarnomasi yuborildi'),
            );
        });
    }
}
