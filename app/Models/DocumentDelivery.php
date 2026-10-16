<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A log entry recording that a document was dispatched to the resident's email
 * (via the staff member's own mail client). See docs/CERTIFICATE_LIFECYCLE.md.
 *
 * @property int $id
 * @property int $document_id
 * @property string $channel
 * @property string $to_email
 * @property int|null $sent_by
 * @property string $status
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read User|null $sender
 */
#[Fillable([
    'document_id', 'channel', 'to_email', 'sent_by', 'status', 'note', 'created_at',
])]
class DocumentDelivery extends Model
{
    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Document, $this> */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /** @return BelongsTo<User, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
