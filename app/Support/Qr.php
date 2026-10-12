<?php

declare(strict_types=1);

namespace App\Support;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

final class Qr
{
    /**
     * Render the given text as a base64 PNG data URI (Besqala teal).
     */
    public static function dataUri(string $text, int $size = 220): string
    {
        $qr = new QrCode(
            data: $text,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 1,
            foregroundColor: new Color(14, 116, 114),
        );

        return (new PngWriter)->write($qr)->getDataUri();
    }
}
