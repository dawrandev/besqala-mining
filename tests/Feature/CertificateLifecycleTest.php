<?php

declare(strict_types=1);

use App\Actions\Application\IssueResidentPermit;
use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;
use Database\Seeders\DemoSeeder;

beforeEach(fn () => $this->seed(DemoSeeder::class));

function authorityUser(): User
{
    return User::query()->where('role', UserRole::Authority)->firstOrFail();
}

function registrationApplication(): Application
{
    return Application::query()->create([
        'number' => 'A-2026-9100',
        'applicant_id' => User::query()->where('role', UserRole::Applicant)->firstOrFail()->id,
        'type' => App\Enums\ApplicationType::Registration,
        'status' => ApplicationStatus::AuthorityReview,
        'company_name' => '«Lifecycle» MChJ',
        'stir' => '301010101',
        'region' => 'beruniy',
        'legal_form' => 'MChJ',
        'address' => 'Beruniy, 1',
        'submitted_at' => now(),
    ]);
}

it('creates the certificate as a draft, not officially issued', function (): void {
    $application = registrationApplication();

    app(IssueResidentPermit::class)->handle($application, authorityUser());

    $certificate = $application->refresh()->documents()->where('kind', DocumentKind::Certificate)->sole();

    expect($certificate->status)->toBe(DocumentStatus::Draft)
        ->and($certificate->status->isOfficiallyIssued())->toBeFalse()
        ->and($certificate->internal_uid)->not->toBeNull()
        ->and($certificate->issued_at)->toBeNull()
        ->and($certificate->isPubliclyVisible())->toBeFalse();
});

it('issues the permit as active immediately', function (): void {
    $application = registrationApplication();

    app(IssueResidentPermit::class)->handle($application, authorityUser());

    $permit = $application->refresh()->documents()->where('kind', DocumentKind::Permit)->sole();

    expect($permit->status)->toBe(DocumentStatus::Active)
        ->and($permit->issued_at)->not->toBeNull();
});

it('records an initial status event for each new document', function (): void {
    $application = registrationApplication();

    app(IssueResidentPermit::class)->handle($application, authorityUser());

    $certificate = $application->refresh()->documents()->where('kind', DocumentKind::Certificate)->sole();
    $event = $certificate->statusEvents()->sole();

    expect($event->from_status)->toBeNull()
        ->and($event->to_status)->toBe(DocumentStatus::Draft)
        ->and($event->actor_id)->toBe(authorityUser()->id);
});

it('excludes draft certificates from the public-visible scope', function (): void {
    $application = registrationApplication();

    app(IssueResidentPermit::class)->handle($application, authorityUser());

    $draftNumbers = App\Models\Document::query()->publiclyVisible()->pluck('number');
    $certificate = $application->refresh()->documents()->where('kind', DocumentKind::Certificate)->sole();

    expect($draftNumbers)->not->toContain($certificate->number);
});
