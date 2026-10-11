<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case CompletenessCheck = 'completeness_check';
    case Deficiency = 'deficiency';
    case ExpertReview = 'expert_review';
    case AuthorityReview = 'authority_review';
    case PermitIssued = 'permit_issued';
    case Registered = 'registered';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('application_status.draft'),
            self::Submitted => __('application_status.submitted'),
            self::CompletenessCheck => __('application_status.completeness_check'),
            self::Deficiency => __('application_status.deficiency'),
            self::ExpertReview => __('application_status.expert_review'),
            self::AuthorityReview => __('application_status.authority_review'),
            self::PermitIssued => __('application_status.permit_issued'),
            self::Registered => __('application_status.registered'),
            self::Rejected => __('application_status.rejected'),
        };
    }

    /**
     * Tailwind colour token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Submitted, self::CompletenessCheck => 'blue',
            self::Deficiency => 'warning',
            self::ExpertReview, self::AuthorityReview => 'indigo',
            self::PermitIssued, self::Registered => 'success',
            self::Rejected => 'error',
        };
    }

    /**
     * Whether the application is still moving through the pipeline.
     */
    public function isInProgress(): bool
    {
        return ! in_array($this, [self::Draft, self::Registered, self::Rejected], true);
    }

    /**
     * The staff role responsible for acting on an application in this status.
     */
    public function ownerRole(): ?UserRole
    {
        return match ($this) {
            self::Submitted, self::CompletenessCheck, self::Deficiency => UserRole::Direksiya,
            self::ExpertReview => UserRole::Expert,
            self::AuthorityReview => UserRole::Authority,
            default => null,
        };
    }
}
