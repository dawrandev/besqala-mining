<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentKind: string
{
    case Certificate = 'certificate';
    case Permit = 'permit';
    case ExpertConclusion = 'expert_conclusion';
    case DeficiencyNotice = 'deficiency_notice';
    case InspectionAct = 'inspection_act';

    public function label(): string
    {
        return match ($this) {
            self::Certificate => __('document_kind.certificate'),
            self::Permit => __('document_kind.permit'),
            self::ExpertConclusion => __('document_kind.expert_conclusion'),
            self::DeficiencyNotice => __('document_kind.deficiency_notice'),
            self::InspectionAct => __('document_kind.inspection_act'),
        };
    }

    /**
     * Prefix used when generating document numbers. The certificate follows the
     * approved template format "BMV-R-YYYY-NNNNNN".
     */
    public function numberPrefix(): string
    {
        return match ($this) {
            self::Certificate => 'BMV-R',
            self::Permit => 'R',
            self::ExpertConclusion => 'E',
            self::DeficiencyNotice => 'K',
            self::InspectionAct => 'D',
        };
    }

    /**
     * Zero-padding width for the sequential part of the number.
     */
    public function numberPadWidth(): int
    {
        return $this === self::Certificate ? 6 : 4;
    }

    /**
     * Whether this document is publicly verifiable by number.
     */
    public function isPubliclyVerifiable(): bool
    {
        return in_array($this, [self::Certificate, self::Permit], true);
    }
}
