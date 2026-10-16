<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Application\CreateApplication;
use App\Actions\Application\IssueResidentPermit;
use App\Actions\Application\RecordExpertConclusion;
use App\Actions\Application\SendDeficiencyNotice;
use App\Actions\Application\TransitionApplication;
use App\Enums\ApplicationStatus;
use App\Enums\ExpertVerdict;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationActionRequest;
use App\Http\Requests\StoreApplicationRequest;
use App\Models\Application;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.applications.index', [
            'applications' => Application::query()
                ->forApplicant($request->user())
                ->with('resident')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('app.applications.create');
    }

    /**
     * Persist a new application (draft or submitted) and return the detail URL.
     */
    public function store(StoreApplicationRequest $request, CreateApplication $createApplication): JsonResponse
    {
        $validated = $request->validated();

        $application = $createApplication->handle([
            'company_name' => $validated['company_name'],
            'stir' => $validated['stir'],
            'region' => $validated['region'],
            'legal_form' => 'MChJ (Mas’uliyati cheklangan jamiyat)',
            'address' => $validated['address'],
            'location_address' => $validated['location_address'] ?? null,
            'cadastre_number' => $validated['cadastre_number'] ?? null,
            'property_right' => $validated['property_right'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'founders' => $request->founders(),
            'power_mw' => $validated['power_mw'] ?? null,
            'energy_source' => $validated['energy_source'] ?? null,
            'bank_account' => $validated['bank_account'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'btc_address' => $validated['btc_address'] ?? null,
        ], $request->user(), $request->wantsSubmit());

        session()->flash('status', $request->wantsSubmit() ? __('Ariza topshirildi.') : __('Qoralama saqlandi.'));

        return response()->json([
            'ok' => true,
            'redirect' => route('app.applications.show', $application),
        ]);
    }

    public function show(Request $request, Application $application): View
    {
        $user = $request->user();

        // Applicants may only open their own applications; staff may open any.
        abort_if(
            ! $user->isStaff() && $application->applicant_id !== $user->id,
            403,
        );

        $application->load(['resident', 'applicant', 'events.actor', 'conclusions.expert', 'deficiencies', 'documents']);

        // Primary verifiable document for the preview/QR card — only officially
        // issued documents (a draft certificate has no public verify page yet).
        $previewDoc = $application->documents
            ->first(fn ($doc): bool => $doc->isPubliclyVisible());

        return view('app.applications.show', [
            'application' => $application,
            'previewDoc' => $previewDoc,
            'previewQr' => $previewDoc !== null
                ? \App\Support\Qr::dataUri(route('verify', ['no' => $previewDoc->number]))
                : null,
        ]);
    }

    /**
     * Perform the role-based workflow action for the application.
     */
    public function action(
        ApplicationActionRequest $request,
        Application $application,
        TransitionApplication $transition,
        SendDeficiencyNotice $deficiency,
        RecordExpertConclusion $conclusion,
        IssueResidentPermit $permit,
    ): JsonResponse {
        $this->authorize('act', $application);

        $user = $request->user();
        $comment = $request->input('comment') ?: null;

        $message = match ($request->string('action')->value()) {
            'submit' => $this->runSubmit($transition, $application, $user),
            'expert' => $this->runSendToExpert($transition, $application, $user),
            'deficiency' => $this->runDeficiency($deficiency, $request, $application, $user),
            'conclude' => $this->runConclude($conclusion, $request, $application, $user, $comment),
            'permit' => $this->runPermit($permit, $application, $user),
            'reject' => $this->runReject($transition, $application, $user, $comment),
            default => throw ValidationException::withMessages(['action' => __('Noto‘g‘ri amal.')]),
        };

        session()->flash('status', $message);

        return response()->json([
            'ok' => true,
            'redirect' => route('app.applications.show', $application),
        ]);
    }

    private function runSubmit(TransitionApplication $transition, Application $application, User $user): string
    {
        $transition->handle($application, ApplicationStatus::Submitted, $user, __('Ariza topshirildi'));

        return __('Ariza topshirildi.');
    }

    private function runSendToExpert(TransitionApplication $transition, Application $application, User $user): string
    {
        $transition->handle($application, ApplicationStatus::ExpertReview, $user, __('Ekspertizaga yuborildi'));

        return __('Ariza ekspertizaga yuborildi.');
    }

    private function runDeficiency(SendDeficiencyNotice $action, ApplicationActionRequest $request, Application $application, User $user): string
    {
        $items = $request->deficiencyItems();

        if ($items === []) {
            throw ValidationException::withMessages([
                'deficiency_text' => __('Kamida bitta kamchilik kiriting.'),
            ]);
        }

        $action->handle($application, $user, $items);

        return __('Kamchilik xabarnomasi yuborildi.');
    }

    private function runConclude(RecordExpertConclusion $action, ApplicationActionRequest $request, Application $application, User $user, ?string $comment): string
    {
        $action->handle($application, $user, ExpertVerdict::from((string) $request->input('verdict')), $comment);

        return __('Ekspert xulosasi saqlandi.');
    }

    private function runPermit(IssueResidentPermit $action, Application $application, User $user): string
    {
        $action->handle($application, $user);

        return __('Ruxsatnoma va guvohnoma berildi.');
    }

    private function runReject(TransitionApplication $transition, Application $application, User $user, ?string $comment): string
    {
        $transition->handle($application, ApplicationStatus::Rejected, $user, __('Ariza rad etildi'), $comment);

        return __('Ariza rad etildi.');
    }
}
