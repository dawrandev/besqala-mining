<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Whether the user may view the application detail page.
     */
    public function view(User $user, Application $application): bool
    {
        return $user->isStaff() || $application->applicant_id === $user->id;
    }

    /**
     * Whether the user may take the workflow action available for the current
     * status. The responsible staff role owns each in-progress status; the
     * applicant owns a draft they created.
     */
    public function act(User $user, Application $application): bool
    {
        if ($application->status === ApplicationStatus::Draft) {
            return $user->role === UserRole::Applicant && $application->applicant_id === $user->id;
        }

        return $user->role === $application->status->ownerRole();
    }
}
