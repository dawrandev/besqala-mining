<?php

declare(strict_types=1);

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Enums\ResidentStatus;
use App\Models\Application;
use App\Models\Document;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class IssueResidentPermit
{
    public function __construct(private readonly TransitionApplication $transition) {}

    /**
     * Register the applicant as a resident, issue the certificate and permit,
     * and move the application to the registered state.
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

            $certificate = Document::query()->create([
                'number' => $this->nextNumber(DocumentKind::Certificate),
                'kind' => DocumentKind::Certificate,
                'status' => DocumentStatus::Active,
                'resident_id' => $resident->id,
                'application_id' => $application->id,
                'issued_by' => $authority->id,
                'payload' => ['companyName' => $resident->company_name],
                'template_version' => 'v1',
                'issued_at' => now(),
                'expires_at' => now()->addYears(5),
            ]);

            Document::query()->create([
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

            $application->update(['resident_id' => $resident->id]);

            return $this->transition->handle(
                $application,
                ApplicationStatus::Registered,
                $authority,
                __('Hujjat rasmiy berildi: :number', ['number' => $certificate->number]),
            );
        });
    }

    private function nextNumber(DocumentKind $kind): string
    {
        $year = now()->year;
        $count = Document::query()->where('kind', $kind)->whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%d-%04d', $kind->numberPrefix(), $year, $count);
    }
}
