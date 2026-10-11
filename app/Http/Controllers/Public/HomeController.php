<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\ResidentStatus;
use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use App\Models\Resident;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'about' => SiteSetting::get('about'),
            'residentCount' => Resident::query()->count(),
            'activeCount' => Resident::query()->where('status', ResidentStatus::Active)->count(),
            'totalPower' => (float) Resident::query()->sum('power_mw'),
            'minedTotal' => (float) Resident::query()->sum('mined_total_btc'),
            'latestNews' => NewsArticle::query()
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
