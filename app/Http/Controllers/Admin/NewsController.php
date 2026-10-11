<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('app.admin.news', [
            'articles' => NewsArticle::query()->latest('published_at')->paginate(15),
        ]);
    }
}
