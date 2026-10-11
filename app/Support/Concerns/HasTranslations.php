<?php

declare(strict_types=1);

namespace App\Support\Concerns;

use Illuminate\Support\Facades\App;

/**
 * Lightweight trilingual content helper for models whose JSON columns hold
 * {"uz": "...", "ru": "...", "en": "..."} shapes. Falls back uz -> any value.
 */
trait HasTranslations
{
    /**
     * Read a translatable attribute in the active (or given) locale.
     */
    public function translate(string $attribute, ?string $locale = null): string
    {
        $value = $this->getAttribute($attribute);

        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        $locale ??= App::getLocale();

        return (string) (
            $value[$locale]
            ?? $value['uz']
            ?? reset($value)
            ?: ''
        );
    }
}
