<?php

declare(strict_types=1);

use App\Actions\Application\IssueResidentPermit;
use App\Actions\Document\IssueCertificate;
use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Document;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => $this->seed(DemoSeeder::class));

function authorityUser(): User
{
    return User::query()->where('role', UserRole::Authority)->firstOrFail();
}

function direksiyaUser(): User
{
    return User::query()->where('role', UserRole::Direksiya)->firstOrFail();
}

/**
 * Create a fresh draft certificate (via the real registration flow) and return it.
 */
function freshDraftCertificate(): Document
{
    $application = registrationApplication();
    app(IssueResidentPermit::class)->handle($application, authorityUser());

    return $application->refresh()->documents()->where('kind', DocumentKind::Certificate)->sole();
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

    $draftNumbers = Document::query()->publiclyVisible()->pluck('number');
    $certificate = $application->refresh()->documents()->where('kind', DocumentKind::Certificate)->sole();

    expect($draftNumbers)->not->toContain($certificate->number);
});

// ---------------------------------------------------------------------------
// Module 2 — official issuance (freeze PDF + SHA-256 + snapshot)
// ---------------------------------------------------------------------------

it('officially issues a draft certificate: freezes the PDF, hash and snapshot', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();

    app(IssueCertificate::class)->handle($certificate, direksiyaUser());
    $certificate->refresh();

    expect($certificate->status)->toBe(DocumentStatus::Active)
        ->and($certificate->issued_by)->toBe(direksiyaUser()->id)
        ->and($certificate->issued_at)->not->toBeNull()
        ->and($certificate->sha256)->toMatch('/^[a-f0-9]{64}$/')
        ->and($certificate->pdf_path)->not->toBeNull()
        ->and($certificate->data_snapshot)->toHaveKey('company_name');

    Storage::disk('documents')->assertExists($certificate->pdf_path);

    // The stored file's hash matches the recorded integrity hash.
    expect(hash('sha256', Storage::disk('documents')->get($certificate->pdf_path)))
        ->toBe($certificate->sha256);

    // A DRAFT -> ACTIVE status event was recorded.
    $last = $certificate->statusEvents()->get()->last();
    expect($last->from_status)->toBe(DocumentStatus::Draft)
        ->and($last->to_status)->toBe(DocumentStatus::Active)
        ->and($last->actor_id)->toBe(direksiyaUser()->id);
});

it('does not change an issued certificate when the resident is later edited', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();
    app(IssueCertificate::class)->handle($certificate, direksiyaUser());
    $certificate->refresh();

    $originalHash = $certificate->sha256;
    $originalBytes = Storage::disk('documents')->get($certificate->pdf_path);

    // Edit the resident card after issuance.
    $certificate->resident->update(['company_name' => '«Changed Name» MChJ']);
    $certificate->refresh();

    expect($certificate->sha256)->toBe($originalHash)
        ->and(Storage::disk('documents')->get($certificate->pdf_path))->toBe($originalBytes)
        ->and($certificate->data_snapshot['company_name'])->not->toBe('«Changed Name» MChJ');
});

it('refuses to issue a certificate that is not a draft', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();
    app(IssueCertificate::class)->handle($certificate, direksiyaUser());

    expect(fn () => app(IssueCertificate::class)->handle($certificate->refresh(), direksiyaUser()))
        ->toThrow(RuntimeException::class);
});

it('refuses to issue a permit through the certificate flow', function (): void {
    Storage::fake('documents');
    $application = registrationApplication();
    app(IssueResidentPermit::class)->handle($application, authorityUser());
    $permit = $application->refresh()->documents()->where('kind', DocumentKind::Permit)->sole();

    expect(fn () => app(IssueCertificate::class)->handle($permit, direksiyaUser()))
        ->toThrow(RuntimeException::class);
});

it('lets the Directorate officially issue a certificate via the endpoint', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();

    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.issue', $certificate))
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect($certificate->refresh()->status)->toBe(DocumentStatus::Active);
});

it('forbids non-Directorate roles from officially issuing', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();

    foreach ([UserRole::Applicant, UserRole::Expert, UserRole::Authority] as $role) {
        $user = User::query()->where('role', $role)->firstOrFail();
        $this->actingAs($user)
            ->postJson(route('app.documents.issue', $certificate))
            ->assertForbidden();
    }

    expect($certificate->refresh()->status)->toBe(DocumentStatus::Draft);
});

it('rejects officially issuing an already active certificate via the endpoint', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();
    app(IssueCertificate::class)->handle($certificate, direksiyaUser());

    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.issue', $certificate->refresh()))
        ->assertStatus(422);
});

it('never serves a draft certificate on the public verify endpoint', function (): void {
    Storage::fake('documents');
    $certificate = freshDraftCertificate();

    // Draft: not public.
    $this->get(route('verify.pdf', ['number' => $certificate->number]))->assertNotFound();

    // After official issuance: public.
    app(IssueCertificate::class)->handle($certificate, direksiyaUser());
    $this->get(route('verify.pdf', ['number' => $certificate->refresh()->number]))->assertOk();
});
