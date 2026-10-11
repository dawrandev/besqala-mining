<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\ApplicationStatus;
use App\Enums\ExpertVerdict;
use App\Enums\ResidentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Document;
use App\Models\ExpertConclusion;
use App\Models\Inspection;
use App\Models\Leader;
use App\Models\NewsArticle;
use App\Models\Resident;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            UserRole::Applicant => $this->applicant($request),
            UserRole::Direksiya => $this->direksiya(),
            UserRole::Expert => $this->expert(),
            UserRole::Authority => $this->authority(),
            UserRole::Admin => $this->admin(),
        };
    }

    private function applicant(Request $request): View
    {
        $user = $request->user();
        $residents = Resident::query()->where('owner_id', $user->id)->get();

        return view('app.dashboard.applicant', [
            'activeResidents' => $residents->where('status', ResidentStatus::Active)->count(),
            'inProgress' => Application::query()->forApplicant($user)->inProgress()->count(),
            'deficiencies' => Application::query()->forApplicant($user)->withStatus(ApplicationStatus::Deficiency)->count(),
            'minedTotal' => (float) $residents->sum(fn (Resident $r) => (float) $r->mined_total_btc),
            'applications' => Application::query()->forApplicant($user)->latest()->limit(5)->get(),
            'certificate' => Document::query()->whereIn('resident_id', $residents->pluck('id'))->where('kind', 'certificate')->first(),
        ]);
    }

    private function direksiya(): View
    {
        return view('app.dashboard.direksiya', [
            'newApplications' => Application::query()->whereIn('status', [ApplicationStatus::Submitted, ApplicationStatus::CompletenessCheck])->count(),
            'atExpert' => Application::query()->withStatus(ApplicationStatus::ExpertReview)->count(),
            'issued' => Application::query()->whereIn('status', [ApplicationStatus::PermitIssued, ApplicationStatus::Registered])->count(),
            'activeResidents' => Resident::query()->where('status', ResidentStatus::Active)->count(),
            'suspendedResidents' => Resident::query()->where('status', ResidentStatus::Suspended)->count(),
            'queue' => Application::query()->inQueueFor(UserRole::Direksiya)->with('resident')->latest('submitted_at')->limit(6)->get(),
            'events' => \App\Models\ApplicationEvent::query()->with('application')->latest()->limit(8)->get(),
        ]);
    }

    private function expert(): View
    {
        return view('app.dashboard.expert', [
            'pending' => Application::query()->inQueueFor(UserRole::Expert)->count(),
            'positive' => ExpertConclusion::query()->where('verdict', ExpertVerdict::Positive)->count(),
            'negative' => ExpertConclusion::query()->where('verdict', ExpertVerdict::Negative)->count(),
            'queue' => Application::query()->inQueueFor(UserRole::Expert)->with('resident')->latest('submitted_at')->limit(8)->get(),
        ]);
    }

    private function authority(): View
    {
        return view('app.dashboard.authority', [
            'pendingPermits' => Application::query()->inQueueFor(UserRole::Authority)->count(),
            'activePermits' => Document::query()->where('kind', 'permit')->where('status', 'active')->count(),
            'suspended' => Resident::query()->where('status', ResidentStatus::Suspended)->count(),
            'violations' => Inspection::query()->where('has_violation', true)->count(),
            'queue' => Application::query()->inQueueFor(UserRole::Authority)->with('resident')->latest('submitted_at')->limit(6)->get(),
            'inspections' => Inspection::query()->with('resident')->latest('conducted_at')->limit(5)->get(),
        ]);
    }

    private function admin(): View
    {
        return view('app.dashboard.admin', [
            'newsCount' => NewsArticle::query()->count(),
            'leadersCount' => Leader::query()->count(),
            'aboutFilled' => \App\Models\SiteSetting::get('about') !== null,
        ]);
    }
}
