<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentStatusEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Reissue a certificate: create a new DRAFT replacement (fresh number and
 * internal id) and mark the previous one REISSUED, linking it to the new one.
 * The replacement is then officially issued through IssueCertificate.
 *
 * See docs/CERTIFICATE_LIFECYCLE.md — the previous certificate is preserved as
 * an immutable historical version and its QR/verify page keeps working.
 */
final class ReissueCertificate
{
    public function __construct(private readonly GenerateDocumentNumber $numbers) {}

    public function handle(Document $previous, User $actor, string $reason): Document
    {
        if ($previous->kind !== DocumentKind::Certificate) {
            throw new RuntimeException('Only certificates can be reissued.');
        }

        if (! $previous->status->canTransitionTo(DocumentStatus::Reissued)) {
            throw new RuntimeException('Only an active or suspended certificate can be reissued.');
        }

        return DB::transaction(function () use ($previous, $actor, $reason): Document {
            $replacement = Document::query()->create([
                'internal_uid' => (string) Str::uuid(),
                'number' => $this->numbers->handle(DocumentKind::Certificate),
                'kind' => DocumentKind::Certificate,
                'status' => DocumentStatus::Draft,
                'resident_id' => $previous->resident_id,
                'application_id' => $previous->application_id,
                'payload' => $previous->payload,
                'template_version' => $previous->template_version,
            ]);

            DocumentStatusEvent::query()->create([
                'document_id' => $replacement->id,
                'from_status' => null,
                'to_status' => DocumentStatus::Draft,
                'reason' => __('Guvohnoma qayta rasmiylashtirish uchun yaratildi'),
                'actor_id' => $actor->id,
                'created_at' => now(),
            ]);

            $from = $previous->status;
            $previous->status = DocumentStatus::Reissued;
            $previous->replaced_by_document_id = $replacement->id;
            $previous->save();

            DocumentStatusEvent::query()->create([
                'document_id' => $previous->id,
                'from_status' => $from,
                'to_status' => DocumentStatus::Reissued,
                'reason' => $reason,
                'actor_id' => $actor->id,
                'created_at' => now(),
            ]);

            return $replacement->refresh();
        });
    }
}
