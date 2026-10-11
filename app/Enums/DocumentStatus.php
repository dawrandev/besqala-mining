<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentStatus: string
{
    case Active = 'active';
    case Revoked = 'revoked';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('document_status.active'),
            self::Revoked => __('document_status.revoked'),
            self::Expired => __('document_status.expired'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Revoked => 'error',
            self::Expired => 'gray',
        };
    }
}
