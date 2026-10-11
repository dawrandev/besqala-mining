<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class TransitionApplication
{
    /**
     * Move an application to a new status and append a timeline event.
     */
    public function handle(
        Application $application,
        ApplicationStatus $to,
        User $actor,
        string $title,
        ?string $note = null,
    ): Application {
        return DB::transaction(function () use ($application, $to, $actor, $title, $note): Application {
            $application->update([
                'status' => $to,
                'decided_at' => in_array($to, [ApplicationStatus::Registered, ApplicationStatus::Rejected, ApplicationStatus::PermitIssued], true)
                    ? now()
                    : $application->decided_at,
            ]);

            ApplicationEvent::query()->create([
                'application_id' => $application->id,
                'actor_id' => $actor->id,
                'type' => $to->value,
                'title' => $title,
                'note' => $note,
            ]);

            return $application->refresh();
        });
    }
}
