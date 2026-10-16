<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Document\ChangeDocumentStatus;
use App\Actions\Document\IssueCertificate;
use App\Actions\Document\ReissueCertificate;
use App\Actions\Document\RenderDocumentPdf;
use App\Enums\DocumentKind;
use App\Enums\DocumentStatus;
use App\Http\Requests\DocumentStatusRequest;
use App\Models\Document;
use App\Models\DocumentDelivery;
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

    /**
     * Apply a certificate lifecycle change (suspend / restore / terminate /
     * annul / reissue). Directorate only; grounds are mandatory.
     */
    public function changeStatus(
        DocumentStatusRequest $request,
        Document $document,
        ChangeDocumentStatus $change,
        ReissueCertificate $reissue,
    ): JsonResponse {
        Gate::forUser($request->user())->authorize('manage', $document);

        $action = $request->string('action')->value();
        $reason = (string) $request->input('reason');
        $user = $request->user();

        $target = match ($action) {
            'suspend' => DocumentStatus::Suspended,
            'restore' => DocumentStatus::Active,
            'terminate' => DocumentStatus::Terminated,
            'annul' => DocumentStatus::Annulled,
            'reissue' => DocumentStatus::Reissued,
            default => null,
        };

        abort_if(
            $target === null || ! $document->status->canTransitionTo($target),
            422,
            __('Bu amalni hujjatning joriy holatida bajarib bo‘lmaydi.'),
        );

        if ($action === 'reissue') {
            abort_if($document->kind !== DocumentKind::Certificate, 422, __('Faqat guvohnomani qayta rasmiylashtirish mumkin.'));
            $reissue->handle($document, $user, $reason);
            $message = __('Guvohnoma qayta rasmiylashtirishga yuborildi (yangi loyiha yaratildi).');
        } else {
            $change->handle($document, $target, $user, $reason);
            $message = __('Hujjat holati yangilandi.');
        }

        session()->flash('status', $message);

        return response()->json([
            'ok' => true,
            'redirect' => $document->application_id !== null
                ? route('app.applications.show', $document->application_id)
                : route('app.residents'),
        ]);
    }

    /**
     * Prepare the certificate email for the resident and log the dispatch.
     *
     * The app does not send mail itself: it returns a mailto: URL (recipient,
     * subject, body with the PDF download and verify links) that the staff
     * member opens in their own mail client. The log records who initiated it,
     * to whom and when (Regulation point 20).
     */
    public function dispatchEmail(Request $request, Document $document): JsonResponse
    {
        Gate::forUser($request->user())->authorize('sendEmail', $document);

        abort_if(
            $document->kind !== DocumentKind::Certificate || ! $document->status->isOfficiallyIssued(),
            422,
            __('Faqat rasmiy berilgan guvohnomani yuborish mumkin.'),
        );

        $resident = $document->resident;
        $snapshotEmail = is_array($document->data_snapshot) ? ($document->data_snapshot['email'] ?? null) : null;
        $email = ($resident !== null ? $resident->email : null) ?? $snapshotEmail;

        abort_if(
            ! is_string($email) || $email === '',
            422,
            __('Rezidentning elektron pochta manzili ko‘rsatilmagan.'),
        );

        $verifyUrl = route('verify', ['no' => $document->number]);
        $pdfUrl = route('verify.pdf', ['number' => $document->number]);

        $subject = __('certificate.email_subject', ['number' => $document->number]);
        $body = __('certificate.email_body', [
            'number' => $document->number,
            'pdf' => $pdfUrl,
            'verify' => $verifyUrl,
        ]);

        $mailto = 'mailto:'.$email.'?subject='.rawurlencode($subject).'&body='.rawurlencode($body);

        DocumentDelivery::query()->create([
            'document_id' => $document->id,
            'channel' => 'email',
            'to_email' => $email,
            'sent_by' => $request->user()->id,
            'status' => 'dispatched',
            'created_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'mailto' => $mailto,
            'message' => __('Email tayyorlandi — pochta dasturingizda oching.'),
        ]);
    }

    private function hasFrozenPdf(Document $document): bool
    {
        return $document->pdf_path !== null
            && Storage::disk('documents')->exists($document->pdf_path);
    }
}
