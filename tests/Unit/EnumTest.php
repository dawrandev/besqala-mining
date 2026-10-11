<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\UserRole;

it('marks every role except applicant as staff', function (): void {
    expect(UserRole::Applicant->isStaff())->toBeFalse()
        ->and(UserRole::Direksiya->isStaff())->toBeTrue()
        ->and(UserRole::Expert->isStaff())->toBeTrue()
        ->and(UserRole::Authority->isStaff())->toBeTrue()
        ->and(UserRole::Admin->isStaff())->toBeTrue();
});

it('routes each in-progress status to the responsible role', function (): void {
    expect(ApplicationStatus::Submitted->ownerRole())->toBe(UserRole::Direksiya)
        ->and(ApplicationStatus::ExpertReview->ownerRole())->toBe(UserRole::Expert)
        ->and(ApplicationStatus::AuthorityReview->ownerRole())->toBe(UserRole::Authority)
        ->and(ApplicationStatus::Draft->ownerRole())->toBeNull();
});

it('knows which statuses are still in progress', function (): void {
    expect(ApplicationStatus::ExpertReview->isInProgress())->toBeTrue()
        ->and(ApplicationStatus::Draft->isInProgress())->toBeFalse()
        ->and(ApplicationStatus::Registered->isInProgress())->toBeFalse()
        ->and(ApplicationStatus::Rejected->isInProgress())->toBeFalse();
});

it('gives each document kind a distinct number prefix', function (): void {
    $prefixes = array_map(fn (DocumentKind $k) => $k->numberPrefix(), DocumentKind::cases());

    expect($prefixes)->toBe(array_unique($prefixes))
        ->and(DocumentKind::Certificate->numberPrefix())->toBe('G');
});
