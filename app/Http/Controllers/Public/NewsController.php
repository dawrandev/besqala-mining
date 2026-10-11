<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('public.news.index', [
            'articles' => NewsArticle::query()
                ->whereNotNull('published_at')
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $article = NewsArticle::query()
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        return view('public.news.show', [
            'article' => $article,
            'related' => NewsArticle::query()
                ->whereNotNull('published_at')
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
