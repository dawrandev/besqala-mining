<?php

declare(strict_types=1);

use App\Enums\DocumentKind;
use App\Enums\UserRole;
use App\Models\Document;
use App\Models\Resident;
use App\Models\User;

function makeDocument(DocumentKind $kind = DocumentKind::Certificate, ?User $owner = null): Document
{
    $resident = Resident::factory()->create(['owner_id' => $owner?->id]);

    return Document::factory()->create([
        'kind' => $kind,
        'number' => $kind->numberPrefix().'-2026-9001',
        'resident_id' => $resident->id,
    ]);
}

it('lets staff download a document pdf', function (): void {
    $staff = User::factory()->role(UserRole::Authority)->create();
    $response = $this->actingAs($staff)->get(route('app.documents.download', makeDocument()));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('lets the owning applicant download their document', function (): void {
    $owner = User::factory()->role(UserRole::Applicant)->create();

    $this->actingAs($owner)
        ->get(route('app.documents.download', makeDocument(DocumentKind::Certificate, $owner)))
        ->assertOk();
});

it('forbids a different applicant from downloading', function (): void {
    $owner = User::factory()->role(UserRole::Applicant)->create();
    $other = User::factory()->role(UserRole::Applicant)->create();

    $this->actingAs($other)
        ->get(route('app.documents.download', makeDocument(DocumentKind::Certificate, $owner)))
        ->assertForbidden();
});

it('streams a publicly verifiable document by number', function (): void {
    $response = $this->get(route('verify.pdf', ['number' => makeDocument(DocumentKind::Permit)->number]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('404s the public pdf for a non-publicly-verifiable document', function (): void {
    $document = makeDocument(DocumentKind::ExpertConclusion);

    $this->get(route('verify.pdf', ['number' => $document->number]))->assertNotFound();
});

it('404s the public pdf for an unknown number', function (): void {
    $this->get(route('verify.pdf', ['number' => 'X-2026-0000']))->assertNotFound();
});
