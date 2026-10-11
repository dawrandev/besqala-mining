<?php

declare(strict_types=1);

namespace App\Enums;

enum ResidentStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('resident_status.active'),
            self::Suspended => __('resident_status.suspended'),
            self::Terminated => __('resident_status.terminated'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'warning',
            self::Terminated => 'error',
        };
    }
}
