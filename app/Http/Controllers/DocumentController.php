<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Document\IssueCertificate;
use App\Actions\Document\RenderDocumentPdf;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function __construct(private readonly RenderDocumentPdf $renderer) {}

    /**
     * Download a document PDF — available to staff and the owning applicant.
     *
     * An officially issued document is served from its frozen stored file so
     * later edits to the resident never change it; a draft is rendered live as
     * a preview.
     */
    public function download(Request $request, Document $document): Response
    {
        Gate::forUser($request->user())->authorize('download', $document);

        if ($this->hasFrozenPdf($document)) {
            return Storage::disk('documents')->download($document->pdf_path, $document->number.'.pdf');
        }

        return $this->renderer->handle($document)->download($document->number.'.pdf');
    }

    /**
     * Stream a publicly verifiable document (certificate/permit) by its number,
     * mirroring the public QR verification flow. Drafts are never public.
     */
    public function verified(string $number): Response
    {
        $document = Document::query()
            ->where('number', $number)
            ->with('resident')
            ->first();

        abort_if(
            $document === null
                || ! $document->kind->isPubliclyVerifiable()
                || ! $document->status->isOfficiallyIssued(),
            404,
        );

        if ($this->hasFrozenPdf($document)) {
            return Storage::disk('documents')->response($document->pdf_path, $document->number.'.pdf');
        }

        return $this->renderer->handle($document)->stream($document->number.'.pdf');
    }

    /**
     * Officially issue (freeze) a draft certificate. Directorate only.
     */
    public function issue(Request $request, Document $document, IssueCertificate $action): JsonResponse
    {
        Gate::forUser($request->user())->authorize('issue', $document);

        abort_if(
            $document->kind !== DocumentKind::Certificate || $document->status !== DocumentStatus::Draft,
            422,
            __('Faqat qoralama holatidagi guvohnomani rasmiy berish mumkin.'),
        );

        $action->handle($document, $request->user());

        session()->flash('status', __('Guvohnoma rasmiy berildi.'));

        return response()->json([
            'ok' => true,
            'redirect' => $document->application_id !== null
                ? route('app.applications.show', $document->application_id)
                : route('app.residents'),
        ]);
    }

    private function hasFrozenPdf(Document $document): bool
    {
        return $document->pdf_path !== null
            && Storage::disk('documents')->exists($document->pdf_path);
    }
}
