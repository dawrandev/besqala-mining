<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationType: string
{
    case Registration = 'registration';
    case Amendment = 'amendment';

    public function label(): string
    {
        return match ($this) {
            self::Registration => __('application_type.registration'),
            self::Amendment => __('application_type.amendment'),
        };
    }
}
