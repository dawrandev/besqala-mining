<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentStatusEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Apply a simple lifecycle status change (suspend, restore, terminate, annul)
 * to a document and record the grounds in its immutable history. Reissue has
 * its own action because it creates a replacement document.
 *
 * See docs/CERTIFICATE_LIFECYCLE.md. The frozen PDF is never touched here.
 */
final class ChangeDocumentStatus
{
    public function handle(Document $document, DocumentStatus $to, User $actor, string $reason): Document
    {
        if ($to === DocumentStatus::Reissued) {
            throw new RuntimeException('Reissue must go through ReissueCertificate.');
        }

        if (! $document->status->canTransitionTo($to)) {
            throw new RuntimeException(sprintf(
                'Cannot change document status from %s to %s.',
                $document->status->value,
                $to->value,
            ));
        }

        return DB::transaction(function () use ($document, $to, $actor, $reason): Document {
            $from = $document->status;

            $document->status = $to;
            $document->save();

            DocumentStatusEvent::query()->create([
                'document_id' => $document->id,
                'from_status' => $from,
                'to_status' => $to,
                'reason' => $reason,
                'actor_id' => $actor->id,
                'created_at' => now(),
            ]);

            return $document->refresh();
        });
    }
}
