<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Contracts\View\View;

class PermitController extends Controller
{
    public function index(): View
    {
        return view('app.permits.index', [
            'applications' => Application::query()
                ->inQueueFor(UserRole::Authority)
                ->with(['resident', 'conclusions'])
                ->latest('submitted_at')
                ->paginate(15),
        ]);
    }
}
