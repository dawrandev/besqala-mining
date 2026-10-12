<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    public function show(Request $request, Application $application): View
    {
        $user = $request->user();

        // Applicants may only open their own applications; staff may open any.
        abort_if(
            ! $user->isStaff() && $application->applicant_id !== $user->id,
            403,
        );

        $application->load(['resident', 'applicant', 'events.actor', 'conclusions.expert', 'deficiencies', 'documents']);

        // Primary verifiable document (certificate preferred) for the preview card.
        $previewDoc = $application->documents
            ->first(fn ($doc): bool => $doc->kind->isPubliclyVerifiable());

        return view('app.applications.show', [
            'application' => $application,
            'previewDoc' => $previewDoc,
            'previewQr' => $previewDoc !== null
                ? \App\Support\Qr::dataUri(route('verify', ['no' => $previewDoc->number]))
                : null,
        ]);
    }
}
