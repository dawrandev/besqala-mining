<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CabinetController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.cabinet.index', [
            'residents' => Resident::query()
                ->where('owner_id', $request->user()->id)
                ->with('documents')
                ->get(),
        ]);
    }

    public function show(Request $request, Resident $resident): View
    {
        abort_if($resident->owner_id !== $request->user()->id, 403);

        $resident->load(['documents', 'applications', 'inspections']);

        return view('app.cabinet.show', ['resident' => $resident]);
    }
}
