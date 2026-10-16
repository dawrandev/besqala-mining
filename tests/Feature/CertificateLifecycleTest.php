<?php

declare(strict_types=1);

use App\Actions\Application\IssueResidentPermit;
use App\Actions\Document\ChangeDocumentStatus;
use App\Actions\Document\IssueCertificate;
use App\Actions\Document\ReissueCertificate;
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

/**
 * A fresh, officially issued (ACTIVE) certificate. Caller must Storage::fake first.
 */
function freshActiveCertificate(): Document
{
    $certificate = freshDraftCertificate();
    app(IssueCertificate::class)->handle($certificate, direksiyaUser());

    return $certificate->refresh();
}

function registrationApplication(): Application
{
    static $seq = 0;
    $seq++;

    return Application::query()->create([
        'number' => 'A-2026-9'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
        'applicant_id' => User::query()->where('role', UserRole::Applicant)->firstOrFail()->id,
        'type' => App\Enums\ApplicationType::Registration,
        'status' => ApplicationStatus::AuthorityReview,
        'company_name' => '«Lifecycle» MChJ',
        'stir' => '3010'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT),
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

// ---------------------------------------------------------------------------
// Module 3 — lifecycle actions (suspend / restore / terminate / annul / reissue)
// ---------------------------------------------------------------------------

it('suspends and restores an active certificate, recording grounds', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    app(ChangeDocumentStatus::class)->handle($certificate, DocumentStatus::Suspended, direksiyaUser(), 'Tekshiruv davomida');
    $certificate->refresh();
    expect($certificate->status)->toBe(DocumentStatus::Suspended);

    app(ChangeDocumentStatus::class)->handle($certificate, DocumentStatus::Active, direksiyaUser(), 'Kamchilik bartaraf etildi');
    $certificate->refresh();
    expect($certificate->status)->toBe(DocumentStatus::Active);

    $events = $certificate->statusEvents()->get();
    $suspend = $events->firstWhere('to_status', DocumentStatus::Suspended);
    expect($suspend->reason)->toBe('Tekshiruv davomida')
        ->and($suspend->actor_id)->toBe(direksiyaUser()->id)
        ->and($events->last()->to_status)->toBe(DocumentStatus::Active);
});

it('terminates and annuls with grounds', function (): void {
    Storage::fake('documents');

    $a = freshActiveCertificate();
    app(ChangeDocumentStatus::class)->handle($a, DocumentStatus::Terminated, direksiyaUser(), 'Muddati tugadi');
    expect($a->refresh()->status)->toBe(DocumentStatus::Terminated);

    $b = freshActiveCertificate();
    app(ChangeDocumentStatus::class)->handle($b, DocumentStatus::Annulled, direksiyaUser(), 'Qoidabuzarlik');
    expect($b->refresh()->status)->toBe(DocumentStatus::Annulled);
});

it('does not touch the frozen PDF when the status changes', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();
    $hash = $certificate->sha256;
    $bytes = Storage::disk('documents')->get($certificate->pdf_path);

    app(ChangeDocumentStatus::class)->handle($certificate, DocumentStatus::Suspended, direksiyaUser(), 'Sabab');
    $certificate->refresh();

    expect($certificate->sha256)->toBe($hash)
        ->and(Storage::disk('documents')->get($certificate->pdf_path))->toBe($bytes);
});

it('rejects an invalid lifecycle transition', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();
    app(ChangeDocumentStatus::class)->handle($certificate, DocumentStatus::Terminated, direksiyaUser(), 'Tugatildi');

    // Terminated is terminal — cannot suspend it.
    expect(fn () => app(ChangeDocumentStatus::class)->handle($certificate->refresh(), DocumentStatus::Suspended, direksiyaUser(), 'x'))
        ->toThrow(RuntimeException::class);
});

it('reissues a certificate: new draft replacement, previous marked reissued', function (): void {
    Storage::fake('documents');
    $previous = freshActiveCertificate();

    $replacement = app(ReissueCertificate::class)->handle($previous, direksiyaUser(), 'Nom o‘zgardi');
    $previous->refresh();

    expect($replacement->status)->toBe(DocumentStatus::Draft)
        ->and($replacement->kind)->toBe(DocumentKind::Certificate)
        ->and($replacement->number)->not->toBe($previous->number)
        ->and($replacement->resident_id)->toBe($previous->resident_id)
        ->and($previous->status)->toBe(DocumentStatus::Reissued)
        ->and($previous->replaced_by_document_id)->toBe($replacement->id);

    // The previous certificate keeps its frozen PDF and public verify page.
    $this->get(route('verify.pdf', ['number' => $previous->number]))->assertOk();
});

it('lets the Directorate change status via the endpoint', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.status', $certificate), ['action' => 'suspend', 'reason' => 'Tekshiruv'])
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect($certificate->refresh()->status)->toBe(DocumentStatus::Suspended);
});

it('forbids non-Directorate roles from lifecycle changes', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    foreach ([UserRole::Applicant, UserRole::Expert, UserRole::Authority] as $role) {
        $user = User::query()->where('role', $role)->firstOrFail();
        $this->actingAs($user)
            ->postJson(route('app.documents.status', $certificate), ['action' => 'suspend', 'reason' => 'Test sabab'])
            ->assertForbidden();
    }

    expect($certificate->refresh()->status)->toBe(DocumentStatus::Active);
});

it('requires grounds for a lifecycle change', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.status', $certificate), ['action' => 'suspend'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['reason']);
});

it('rejects an impossible transition via the endpoint', function (): void {
    $certificate = freshDraftCertificate();

    // A draft cannot be suspended.
    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.status', $certificate), ['action' => 'suspend', 'reason' => 'Test sabab'])
        ->assertStatus(422);
});

// ---------------------------------------------------------------------------
// Module 4 — public registry/verification page (Regulation point 21)
// ---------------------------------------------------------------------------

it('shows the public registry record for an issued certificate', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    $this->get(route('verify', ['no' => $certificate->number]))
        ->assertOk()
        ->assertSee($certificate->number)
        ->assertSee('«Lifecycle» MChJ')
        ->assertSee(__('Reestr yozuvi'))
        ->assertSee(__('Amaldagi guvohnoma'));
});

it('never reveals a draft certificate on the public page', function (): void {
    $certificate = freshDraftCertificate();

    $this->get(route('verify', ['no' => $certificate->number]))
        ->assertOk()
        ->assertSee(__('Hujjat topilmadi'))
        ->assertDontSee('«Lifecycle» MChJ');
});

it('keeps the public page available with history after suspension', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();
    app(ChangeDocumentStatus::class)->handle($certificate, DocumentStatus::Suspended, direksiyaUser(), 'Hujjatlar tekshirilmoqda');

    $this->get(route('verify', ['no' => $certificate->refresh()->number]))
        ->assertOk()
        ->assertSee(__('Amal vaqtincha to‘xtatilgan'))
        ->assertSee(__('Holat o‘zgarishlari tarixi'))
        ->assertSee('Hujjatlar tekshirilmoqda');
});

it('links a reissued certificate to its replacement on the public page', function (): void {
    Storage::fake('documents');
    $previous = freshActiveCertificate();
    $replacement = app(ReissueCertificate::class)->handle($previous, direksiyaUser(), 'Nom o‘zgardi');

    $this->get(route('verify', ['no' => $previous->refresh()->number]))
        ->assertOk()
        ->assertSee(__('Qayta rasmiylashtirilgan'))
        ->assertSee($replacement->number);
});

it('does not expose staff identities on the public page', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();
    app(ChangeDocumentStatus::class)->handle($certificate, DocumentStatus::Suspended, direksiyaUser(), 'Sabab matni');

    $this->get(route('verify', ['no' => $certificate->refresh()->number]))
        ->assertOk()
        ->assertDontSee(direksiyaUser()->name);
});

// ---------------------------------------------------------------------------
// Module 5 — certificate number format (BMV-R-YYYY-NNNNNN)
// ---------------------------------------------------------------------------

it('numbers certificates in the approved BMV-R format', function (): void {
    $certificate = freshDraftCertificate();

    expect($certificate->number)->toMatch('/^BMV-R-\d{4}-\d{6}$/');
});

it('renders a certificate PDF without error', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    $bytes = app(App\Actions\Document\RenderDocumentPdf::class)->handle($certificate)->output();

    expect($bytes)->toStartWith('%PDF')->and(strlen($bytes))->toBeGreaterThan(10000);
});

// ---------------------------------------------------------------------------
// Module 6 — email dispatch via mailto (no SMTP) + delivery log
// ---------------------------------------------------------------------------

it('prepares the certificate email (mailto) and logs the dispatch', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();
    $certificate->resident->update(['email' => 'resident@example.uz']);

    $response = $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.email', $certificate))
        ->assertOk()
        ->assertJson(['ok' => true]);

    $mailto = $response->json('mailto');
    expect($mailto)->toStartWith('mailto:resident@example.uz?')
        ->and($mailto)->toContain($certificate->number);

    $this->assertDatabaseHas('document_deliveries', [
        'document_id' => $certificate->id,
        'to_email' => 'resident@example.uz',
        'sent_by' => direksiyaUser()->id,
        'status' => 'dispatched',
    ]);
});

it('forbids non-Directorate roles from emailing', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();
    $certificate->resident->update(['email' => 'resident@example.uz']);

    foreach ([UserRole::Applicant, UserRole::Expert, UserRole::Authority] as $role) {
        $user = User::query()->where('role', $role)->firstOrFail();
        $this->actingAs($user)
            ->postJson(route('app.documents.email', $certificate))
            ->assertForbidden();
    }

    $this->assertDatabaseCount('document_deliveries', 0);
});

it('does not email a draft certificate', function (): void {
    $certificate = freshDraftCertificate();

    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.email', $certificate))
        ->assertStatus(422);
});

it('rejects emailing when the resident has no email', function (): void {
    Storage::fake('documents');
    $certificate = freshActiveCertificate();

    $this->actingAs(direksiyaUser())
        ->postJson(route('app.documents.email', $certificate))
        ->assertStatus(422);
});
