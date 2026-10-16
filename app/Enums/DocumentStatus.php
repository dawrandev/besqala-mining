<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Certificate (and document) lifecycle statuses.
 *
 * DRAFT is internal: such a document is not considered officially issued and
 * must not appear in the public registry or be emailed. See
 * docs/CERTIFICATE_LIFECYCLE.md.
 */
enum DocumentStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Suspended = 'suspended';
    case Reissued = 'reissued';
    case Terminated = 'terminated';
    case Annulled = 'annulled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('document_status.draft'),
            self::Active => __('document_status.active'),
            self::Suspended => __('document_status.suspended'),
            self::Reissued => __('document_status.reissued'),
            self::Terminated => __('document_status.terminated'),
            self::Annulled => __('document_status.annulled'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Active => 'success',
            self::Suspended => 'warning',
            self::Reissued => 'blue',
            self::Terminated => 'gray',
            self::Annulled => 'error',
        };
    }

    /**
     * Whether a document in this status is officially issued, i.e. it has a
     * frozen PDF and may appear in the public registry and be emailed.
     */
    public function isOfficiallyIssued(): bool
    {
        return $this !== self::Draft;
    }

    /**
     * Whether the document is currently valid (actively in force).
     */
    public function isInForce(): bool
    {
        return $this === self::Active;
    }

    /**
     * Whether a lifecycle transition from this status to the given one is
     * allowed. DRAFT leaves only through official issuance (handled by
     * IssueCertificate); REISSUED/TERMINATED/ANNULLED are terminal.
     */
    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Active => in_array($to, [self::Suspended, self::Terminated, self::Annulled, self::Reissued], true),
            self::Suspended => in_array($to, [self::Active, self::Terminated, self::Annulled, self::Reissued], true),
            self::Draft, self::Reissued, self::Terminated, self::Annulled => false,
        };
    }
}
