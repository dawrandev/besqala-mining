<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use Illuminate\Contracts\View\View;

class OversightController extends Controller
{
    public function index(): View
    {
        return view('app.oversight.index', [
            'inspections' => Inspection::query()
                ->with('resident')
                ->latest('conducted_at')
                ->paginate(15),
        ]);
    }
}
