<?php

declare(strict_types=1);

namespace App\Enums;

enum ExpertVerdict: string
{
    case Positive = 'positive';
    case Negative = 'negative';

    public function label(): string
    {
        return match ($this) {
            self::Positive => __('expert_verdict.positive'),
            self::Negative => __('expert_verdict.negative'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Positive => 'success',
            self::Negative => 'error',
        };
    }
}
