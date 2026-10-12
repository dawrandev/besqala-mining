<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Document\RenderDocumentPdf;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function __construct(private readonly RenderDocumentPdf $renderer) {}

    /**
     * Download a document PDF — available to staff and the owning applicant.
     */
    public function download(Request $request, Document $document): Response
    {
        Gate::forUser($request->user())->authorize('download', $document);

        return $this->renderer->handle($document)->download($document->number.'.pdf');
    }

    /**
     * Stream a publicly verifiable document (certificate/permit) by its number,
     * mirroring the public QR verification flow.
     */
    public function verified(string $number): Response
    {
        $document = Document::query()
            ->where('number', $number)
            ->with('resident')
            ->first();

        abort_if($document === null || ! $document->kind->isPubliclyVerifiable(), 404);

        return $this->renderer->handle($document)->stream($document->number.'.pdf');
    }
}
