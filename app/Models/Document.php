<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string|null $internal_uid
 * @property string $number
 * @property DocumentKind $kind
 * @property DocumentStatus $status
 * @property int|null $resident_id
 * @property int|null $application_id
 * @property int|null $issued_by
 * @property array<string, mixed>|null $payload
 * @property array<string, mixed>|null $data_snapshot
 * @property string|null $template_version
 * @property string|null $sha256
 * @property string|null $pdf_path
 * @property int|null $replaced_by_document_id
 * @property \Illuminate\Support\Carbon|null $issued_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property-read Resident|null $resident
 * @property-read Document|null $replacedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DocumentStatusEvent> $statusEvents
 */
#[Fillable([
    'internal_uid', 'number', 'kind', 'status', 'resident_id', 'application_id', 'issued_by',
    'payload', 'data_snapshot', 'template_version', 'sha256', 'pdf_path',
    'replaced_by_document_id', 'issued_at', 'expires_at',
])]
class Document extends Model
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => DocumentKind::class,
            'status' => DocumentStatus::class,
            'payload' => 'array',
            'data_snapshot' => 'array',
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Resident, $this> */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /** @return BelongsTo<Application, $this> */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /** @return BelongsTo<User, $this> */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /** @return HasMany<DocumentStatusEvent, $this> */
    public function statusEvents(): HasMany
    {
        return $this->hasMany(DocumentStatusEvent::class)->oldest();
    }

    /** @return HasMany<DocumentDelivery, $this> */
    public function deliveries(): HasMany
    {
        return $this->hasMany(DocumentDelivery::class)->latest('created_at');
    }

    /** The document that replaced this one when it was reissued. */
    /** @return BelongsTo<Document, $this> */
    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'replaced_by_document_id');
    }

    /** Documents that this one replaced (reissue predecessors). */
    /** @return HasMany<Document, $this> */
    public function replaces(): HasMany
    {
        return $this->hasMany(Document::class, 'replaced_by_document_id');
    }

    /**
     * Whether this document may be shown on the public registry/verification
     * page: it is officially issued (not a draft) and its kind is public.
     */
    public function isPubliclyVisible(): bool
    {
        return $this->status->isOfficiallyIssued() && $this->kind->isPubliclyVerifiable();
    }

    /**
     * Scope to documents eligible for public display.
     *
     * @param  Builder<Document>  $query
     */
    public function scopePubliclyVisible(Builder $query): void
    {
        $query->where('status', '!=', DocumentStatus::Draft->value)
            ->whereIn('kind', [DocumentKind::Certificate->value, DocumentKind::Permit->value]);
    }
}
