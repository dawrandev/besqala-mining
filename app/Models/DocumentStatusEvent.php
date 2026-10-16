<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An immutable audit entry recording a change of a document's lifecycle status
 * (including the initial creation and restorations). See
 * docs/CERTIFICATE_LIFECYCLE.md.
 *
 * @property int $id
 * @property int $document_id
 * @property DocumentStatus|null $from_status
 * @property DocumentStatus $to_status
 * @property string|null $reason
 * @property int|null $actor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read User|null $actor
 */
#[Fillable([
    'document_id', 'from_status', 'to_status', 'reason', 'actor_id', 'created_at',
])]
class DocumentStatusEvent extends Model
{
    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'from_status' => DocumentStatus::class,
            'to_status' => DocumentStatus::class,
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Document, $this> */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * A public, privacy-safe label for this lifecycle change (no actor name),
     * used on the public registry/verification page.
     */
    public function publicLabel(): string
    {
        return match (true) {
            $this->to_status === DocumentStatus::Active && $this->from_status === DocumentStatus::Suspended => __('Amal tiklandi'),
            $this->to_status === DocumentStatus::Active => __('Guvohnoma berildi'),
            $this->to_status === DocumentStatus::Suspended => __('Amal to‘xtatildi'),
            $this->to_status === DocumentStatus::Terminated => __('Amal tugatildi'),
            $this->to_status === DocumentStatus::Annulled => __('Bekor qilindi'),
            $this->to_status === DocumentStatus::Reissued => __('Qayta rasmiylashtirildi'),
            default => $this->to_status->label(),
        };
    }

    /**
     * Whether this event should appear on the public history (drafts are internal).
     */
    public function isPublic(): bool
    {
        return $this->to_status !== DocumentStatus::Draft;
    }
}
