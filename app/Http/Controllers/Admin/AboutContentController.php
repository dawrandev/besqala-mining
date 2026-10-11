<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;

class AboutContentController extends Controller
{
    public function index(): View
    {
        return view('app.admin.about', [
            'about' => SiteSetting::get('about'),
        ]);
    }
}
