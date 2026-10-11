<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\ApplicationStatus;
use App\Enums\ResidentStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Document;
use App\Models\Resident;
use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('app.reports.index', [
            'totalApplications' => Application::query()->count(),
            'registered' => Application::query()->withStatus(ApplicationStatus::Registered)->count(),
            'rejected' => Application::query()->withStatus(ApplicationStatus::Rejected)->count(),
            'activeResidents' => Resident::query()->where('status', ResidentStatus::Active)->count(),
            'suspendedResidents' => Resident::query()->where('status', ResidentStatus::Suspended)->count(),
            'certificates' => Document::query()->where('kind', 'certificate')->count(),
            'byStatus' => Application::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }
}
