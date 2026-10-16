<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Enums\DocumentKind;
use App\Models\Document;

/**
 * Generate the next sequential, human-facing document number for a kind,
 * e.g. "G-2026-0001". (The number format is revisited in Module 5.)
 */
final class GenerateDocumentNumber
{
    public function handle(DocumentKind $kind): string
    {
        $year = now()->year;

        $count = Document::query()
            ->where('kind', $kind)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('%s-%d-%04d', $kind->numberPrefix(), $year, $count);
    }
}
