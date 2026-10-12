<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

final class RenderDocumentPdf
{
    /**
     * Build a printable, QR-verifiable PDF for the given document.
     */
    public function handle(Document $document): PdfDocument
    {
        $document->loadMissing(['resident', 'issuer', 'application']);

        $verifyUrl = route('verify', ['no' => $document->number]);

        return Pdf::loadView('pdf.document', [
            'document' => $document,
            'resident' => $document->resident,
            'verifyUrl' => $verifyUrl,
            'qr' => $this->qrDataUri($verifyUrl),
        ])->setPaper('a4');
    }

    /**
     * Render the verification URL as a base64 PNG data URI for embedding.
     */
    private function qrDataUri(string $url): string
    {
        $qr = new QrCode(
            data: $url,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 240,
            margin: 1,
            foregroundColor: new Color(14, 116, 114),
        );

        return (new PngWriter)->write($qr)->getDataUri();
    }
}
