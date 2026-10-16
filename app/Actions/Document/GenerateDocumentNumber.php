<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Enums\DocumentKind;
use App\Models\Document;

/**
 * Generate the next sequential, human-facing document number for a kind,
 * e.g. "BMV-R-2026-000001".
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

        $sequence = str_pad((string) $count, $kind->numberPadWidth(), '0', STR_PAD_LEFT);

        return sprintf('%s-%d-%s', $kind->numberPrefix(), $year, $sequence);
    }
}
