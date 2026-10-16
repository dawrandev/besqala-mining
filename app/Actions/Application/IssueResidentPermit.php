<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Enums\ResidentStatus;
use App\Models\Application;
use App\Models\Document;
use App\Models\DocumentStatusEvent;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class IssueResidentPermit
{
    public function __construct(private readonly TransitionApplication $transition) {}

    /**
     * Register the applicant as a resident, create the certificate as a DRAFT
     * (officially issued later by the Directorate) and issue the permit, then
     * move the application to the registered state.
     *
     * See docs/CERTIFICATE_LIFECYCLE.md — the certificate PDF is frozen only at
     * official issuance, not here.
     */
    public function handle(Application $application, User $authority): Application
    {
        return DB::transaction(function () use ($application, $authority): Application {
            $resident = $application->resident ?? Resident::query()->create([
                'owner_id' => $application->applicant_id,
                'company_name' => $application->company_name,
                'stir' => $application->stir ?? (string) random_int(300000000, 399999999),
                'region' => $application->region,
                'legal_form' => $application->legal_form,
                'status' => ResidentStatus::Active,
                'address' => $application->address ?? '—',
                'location_address' => $application->location_address,
                'email' => $application->email,
                'phone' => $application->phone,
                'founders' => $application->founders,
                'power_mw' => $application->power_mw,
                'energy_source' => $application->energy_source,
                'registered_at' => now(),
            ]);

            // Certificate starts as a DRAFT: editable and regenerable until the
            // Directorate officially issues (freezes) it.
            $certificate = Document::query()->create([
                'internal_uid' => (string) Str::uuid(),
                'number' => $this->nextNumber(DocumentKind::Certificate),
                'kind' => DocumentKind::Certificate,
                'status' => DocumentStatus::Draft,
                'resident_id' => $resident->id,
                'application_id' => $application->id,
                'issued_by' => null,
                'payload' => ['companyName' => $resident->company_name],
                'template_version' => 'v1',
            ]);
            $this->recordStatus($certificate, null, DocumentStatus::Draft, $authority, __('Guvohnoma loyihasi yaratildi'));

            // The permit is issued directly (active) by the authority.
            $permit = Document::query()->create([
                'internal_uid' => (string) Str::uuid(),
                'number' => $this->nextNumber(DocumentKind::Permit),
                'kind' => DocumentKind::Permit,
                'status' => DocumentStatus::Active,
                'resident_id' => $resident->id,
                'application_id' => $application->id,
                'issued_by' => $authority->id,
                'payload' => ['scope' => 'Kripto-mayning faoliyati'],
                'issued_at' => now(),
                'expires_at' => now()->addYears(5),
            ]);
            $this->recordStatus($permit, null, DocumentStatus::Active, $authority, __('Ruxsatnoma berildi'));

            $application->update(['resident_id' => $resident->id]);

            return $this->transition->handle(
                $application,
                ApplicationStatus::Registered,
                $authority,
                __('Rezident ro‘yxatga olindi; guvohnoma loyihasi tayyorlandi'),
            );
        });
    }

    private function recordStatus(Document $document, ?DocumentStatus $from, DocumentStatus $to, User $actor, ?string $reason): void
    {
        DocumentStatusEvent::query()->create([
            'document_id' => $document->id,
            'from_status' => $from,
            'to_status' => $to,
            'reason' => $reason,
            'actor_id' => $actor->id,
            'created_at' => now(),
        ]);
    }

    private function nextNumber(DocumentKind $kind): string
    {
        $year = now()->year;
        $count = Document::query()->where('kind', $kind)->whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%d-%04d', $kind->numberPrefix(), $year, $count);
    }
}
