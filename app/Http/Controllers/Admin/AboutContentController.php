<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAboutRequest;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class AboutContentController extends Controller
{
    public function index(): View
    {
        return view('app.admin.about', [
            'about' => SiteSetting::get('about'),
        ]);
    }

    public function update(UpdateAboutRequest $request): JsonResponse
    {
        SiteSetting::put('about', [
            'title' => $request->input('title'),
            'intro' => $request->input('intro'),
            'stats' => $request->stats(),
        ]);

        return response()->json(['ok' => true, 'message' => __('Saqlandi.')]);
    }
}
