<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Contracts\View\View;

class LeadershipController extends Controller
{
    public function index(): View
    {
        return view('app.admin.leadership', [
            'leaders' => Leader::query()->orderBy('sort_order')->get(),
        ]);
    }
}
