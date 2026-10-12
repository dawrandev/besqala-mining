<?php

declare(strict_types=1);

namespace App\Enums;

enum NewsCategory: string
{
    case Official = 'official';
    case Residents = 'residents';
    case Energy = 'energy';

    public function label(): string
    {
        return match ($this) {
            self::Official => __('news_category.official'),
            self::Residents => __('news_category.residents'),
            self::Energy => __('news_category.energy'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Official => 'brand',
            self::Residents => 'success',
            self::Energy => 'warning',
        };
    }
}
