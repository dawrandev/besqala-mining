<?php

declare(strict_types=1);

namespace App\Actions\Document;

use App\Enums\DocumentKind;
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
     *
     * Certificates use the approved A4 template and render from the immutable
     * snapshot (falling back to the live resident); other documents use the
     * generic layout.
     */
    public function handle(Document $document): PdfDocument
    {
        $document->loadMissing(['resident', 'issuer', 'application']);

        $verifyUrl = route('verify', ['no' => $document->number]);
        $qr = $this->qrDataUri($verifyUrl);

        if ($document->kind === DocumentKind::Certificate) {
            return Pdf::loadView('pdf.certificate', [
                'document' => $document,
                'data' => $this->certificateData($document),
                'verifyUrl' => $verifyUrl,
                'qr' => $qr,
                'frame' => $this->frameDataUri(),
            ])->setPaper('a4');
        }

        return Pdf::loadView('pdf.document', [
            'document' => $document,
            'resident' => $document->resident,
            'verifyUrl' => $verifyUrl,
            'qr' => $qr,
        ])->setPaper('a4');
    }

    /**
     * Certificate field values — the frozen snapshot is authoritative, with the
     * live resident as a fallback for documents issued before snapshots existed.
     *
     * @return array<string, string|null>
     */
    private function certificateData(Document $document): array
    {
        $snapshot = $document->data_snapshot ?? [];
        $resident = $document->resident;

        // Live values (snapshot takes precedence over these when present).
        $live = [
            'company_name' => $resident?->company_name,
            'address' => $resident?->address,
            'location_address' => $resident?->location_address,
            'email' => $resident?->email,
            'stir' => $resident?->stir,
            'phone' => $resident?->phone,
        ];

        $data = [];
        foreach ($live as $key => $value) {
            $data[$key] = $snapshot[$key] ?? $value;
        }

        $payloadName = is_array($document->payload) ? ($document->payload['companyName'] ?? null) : null;
        $data['company_name'] ??= $payloadName;

        return $data;
    }

    /**
     * A decorative green beaded frame for the certificate, built as an inline
     * SVG (plain shapes only, so dompdf renders it reliably). Returned as a
     * data URI for use as a full-page background image.
     */
    private function frameDataUri(): string
    {
        $w = 794;
        $h = 1123;
        $green = '#2f6b5e';
        $inset = 20;
        $step = 14;
        $r = 2.2;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'">';
        $svg .= '<rect x="14" y="14" width="'.($w - 28).'" height="'.($h - 28).'" fill="none" stroke="'.$green.'" stroke-width="2.5"/>';
        $svg .= '<rect x="26" y="26" width="'.($w - 52).'" height="'.($h - 52).'" fill="none" stroke="'.$green.'" stroke-width="1"/>';

        for ($x = $inset; $x <= $w - $inset; $x += $step) {
            $svg .= '<circle cx="'.$x.'" cy="'.$inset.'" r="'.$r.'" fill="'.$green.'"/>';
            $svg .= '<circle cx="'.$x.'" cy="'.($h - $inset).'" r="'.$r.'" fill="'.$green.'"/>';
        }
        for ($y = $inset; $y <= $h - $inset; $y += $step) {
            $svg .= '<circle cx="'.$inset.'" cy="'.$y.'" r="'.$r.'" fill="'.$green.'"/>';
            $svg .= '<circle cx="'.($w - $inset).'" cy="'.$y.'" r="'.$r.'" fill="'.$green.'"/>';
        }
        $svg .= '</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
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
