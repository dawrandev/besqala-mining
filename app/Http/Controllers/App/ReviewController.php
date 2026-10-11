<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Contracts\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('app.review.index', [
            'applications' => Application::query()
                ->inQueueFor(UserRole::Expert)
                ->with('resident')
                ->latest('submitted_at')
                ->paginate(15),
        ]);
    }
}
