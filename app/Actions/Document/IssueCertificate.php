<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentStatusEvent;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Officially issue a DRAFT certificate: freeze its PDF to private storage with
 * a SHA-256 integrity hash and an immutable snapshot of the resident data, then
 * move it to ACTIVE. Performed by the Directorate.
 *
 * See docs/CERTIFICATE_LIFECYCLE.md. The frozen PDF is never re-rendered: later
 * edits to the resident card do not change an already issued certificate.
 */
final class IssueCertificate
{
    private const TEMPLATE_VERSION = 'v1';

    private const VALIDITY_YEARS = 5;

    public function __construct(private readonly RenderDocumentPdf $renderer) {}

    public function handle(Document $certificate, User $issuer): Document
    {
        $this->guard($certificate);

        $certificate->loadMissing('resident');
        $resident = $certificate->resident;

        if (! $resident instanceof Resident) {
            throw new RuntimeException('Certificate has no resident to issue for.');
        }

        $issuedAt = now();

        // Populate the final issued state in memory so the rendered PDF carries
        // the issue date and frozen values before persistence.
        $certificate->fill([
            'status' => DocumentStatus::Active,
            'issued_by' => $issuer->id,
            'issued_at' => $issuedAt,
            'expires_at' => now()->addYears(self::VALIDITY_YEARS),
            'template_version' => self::TEMPLATE_VERSION,
            'data_snapshot' => $this->snapshot($resident),
        ]);

        $bytes = $this->renderer->handle($certificate)->output();
        $sha256 = hash('sha256', $bytes);
        $path = 'certificates/'.$certificate->internal_uid.'.pdf';

        Storage::disk('documents')->put($path, $bytes);

        try {
            DB::transaction(function () use ($certificate, $issuer, $path, $sha256): void {
                $certificate->pdf_path = $path;
                $certificate->sha256 = $sha256;
                $certificate->save();

                DocumentStatusEvent::query()->create([
                    'document_id' => $certificate->id,
                    'from_status' => DocumentStatus::Draft,
                    'to_status' => DocumentStatus::Active,
                    'reason' => __('Guvohnoma rasmiy berildi'),
                    'actor_id' => $issuer->id,
                    'created_at' => now(),
                ]);
            });
        } catch (Throwable $e) {
            // Keep storage and DB consistent if persistence fails.
            Storage::disk('documents')->delete($path);

            throw $e;
        }

        return $certificate->refresh();
    }

    /**
     * Reject anything that is not a draft certificate.
     */
    private function guard(Document $certificate): void
    {
        if ($certificate->kind !== DocumentKind::Certificate) {
            throw new RuntimeException('Only certificates can be officially issued here.');
        }

        if ($certificate->status !== DocumentStatus::Draft) {
            throw new RuntimeException('Only a draft certificate can be officially issued.');
        }
    }

    /**
     * Immutable snapshot of the resident data captured at issuance.
     *
     * @return array<string, mixed>
     */
    private function snapshot(Resident $resident): array
    {
        return [
            'company_name' => $resident->company_name,
            'legal_form' => $resident->legal_form,
            'region' => $resident->region->value,
            'address' => $resident->address,
            'location_address' => $resident->location_address,
            'email' => $resident->email,
            'stir' => $resident->stir,
            'phone' => $resident->phone,
            'founders' => $resident->founders ?? [],
            'power_mw' => $resident->power_mw,
            'energy_source' => $resident->energy_source,
            'captured_at' => now()->toIso8601String(),
        ];
    }
}
