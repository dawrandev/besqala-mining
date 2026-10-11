<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Contracts\View\View;

class ResidentController extends Controller
{
    public function index(): View
    {
        return view('app.residents.index', [
            'residents' => Resident::query()
                ->withCount('applications')
                ->latest('registered_at')
                ->paginate(15),
        ]);
    }
}
