<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RegistryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $region = $request->string('region')->toString();

        $residents = Resident::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('stir', 'like', "%{$search}%");
                });
            })
            ->when($region !== '', fn ($query) => $query->where('region', $region))
            ->with(['documents' => fn ($q) => $q->where('kind', 'certificate')])
            ->orderBy('company_name')
            ->get();

        return view('public.registry', [
            'residents' => $residents,
            'search' => $search,
            'region' => $region,
        ]);
    }
}
