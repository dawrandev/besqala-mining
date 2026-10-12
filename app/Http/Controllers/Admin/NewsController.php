<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\NewsCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('app.admin.news', [
            'articles' => $this->paginated(),
        ]);
    }

    /**
     * The paginated table body, rendered as HTML for AJAX refresh.
     */
    public function list(): View
    {
        return view('app.admin.partials.news-table', [
            'articles' => $this->paginated(),
        ]);
    }

    /**
     * Pagination whose links always point at the AJAX list endpoint, so the
     * table container can reload any page in place.
     */
    private function paginated(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return NewsArticle::query()
            ->latest('published_at')
            ->paginate(15)
            ->withPath(route('app.admin.news.list'));
    }

    /**
     * Field values for the edit form (JSON).
     */
    public function edit(NewsArticle $news): JsonResponse
    {
        return response()->json([
            'id' => $news->id,
            'title' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $news->title),
            'excerpt' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $news->excerpt ?? []),
            'body' => array_merge(['uz' => '', 'ru' => '', 'en' => ''], $news->body),
            'category' => $news->category instanceof NewsCategory ? $news->category->value : 'official',
            'published' => $news->isPublished(),
        ]);
    }

    public function store(StoreNewsRequest $request): JsonResponse
    {
        $data = $this->payload($request);
        $data['slug'] = Str::slug((string) $request->input('title.uz')).'-'.Str::random(5);

        NewsArticle::query()->create($data);

        return response()->json(['ok' => true, 'message' => __('Saqlandi.')]);
    }

    public function update(StoreNewsRequest $request, NewsArticle $news): JsonResponse
    {
        $news->update($this->payload($request));

        return response()->json(['ok' => true, 'message' => __('Saqlandi.')]);
    }

    public function destroy(NewsArticle $news): JsonResponse
    {
        $news->delete();

        return response()->json(['ok' => true, 'message' => __('O‘chirildi.')]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreNewsRequest $request): array
    {
        return [
            'title' => $request->input('title'),
            'excerpt' => $request->input('excerpt', []),
            'body' => $request->input('body'),
            'category' => NewsCategory::tryFrom((string) $request->input('category')),
            'published_at' => $request->boolean('published') ? now() : null,
            'author_id' => $request->user()->id,
        ];
    }
}
