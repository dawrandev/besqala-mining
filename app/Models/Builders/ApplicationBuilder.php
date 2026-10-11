<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \App\Models\Application
 *
 * @extends Builder<\App\Models\Application>
 */
final class ApplicationBuilder extends Builder
{
    /**
     * Applications awaiting action from a given staff role.
     */
    public function inQueueFor(UserRole $role): self
    {
        $statuses = match ($role) {
            UserRole::Direksiya => [ApplicationStatus::Submitted, ApplicationStatus::CompletenessCheck, ApplicationStatus::Deficiency],
            UserRole::Expert => [ApplicationStatus::ExpertReview],
            UserRole::Authority => [ApplicationStatus::AuthorityReview],
            default => [],
        };

        return $this->whereIn('status', $statuses);
    }

    public function withStatus(ApplicationStatus $status): self
    {
        return $this->where('status', $status);
    }

    public function inProgress(): self
    {
        return $this->whereNotIn('status', [
            ApplicationStatus::Draft,
            ApplicationStatus::Registered,
            ApplicationStatus::Rejected,
        ]);
    }

    public function forApplicant(User $user): self
    {
        return $this->where('applicant_id', $user->id);
    }
}
