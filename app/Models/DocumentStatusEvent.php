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
}
