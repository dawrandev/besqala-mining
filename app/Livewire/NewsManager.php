<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\NewsCategory;
use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class NewsManager extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?int $editingId = null;

    /** @var array<string, string> */
    public array $title = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var array<string, string> */
    public array $excerpt = ['uz' => '', 'ru' => '', 'en' => ''];

    /** @var array<string, string> */
    public array $body = ['uz' => '', 'ru' => '', 'en' => ''];

    public bool $published = true;

    public string $category = 'official';

    public function create(): void
    {
        $this->reset(['editingId', 'title', 'excerpt', 'body']);
        $this->published = true;
        $this->category = 'official';
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $article = NewsArticle::query()->findOrFail($id);
        $this->editingId = $article->id;
        $this->title = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $article->title);
        $this->excerpt = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $article->excerpt ?? []);
        $this->body = array_merge(['uz' => '', 'ru' => '', 'en' => ''], $article->body);
        $this->published = $article->isPublished();
        $this->category = $article->category instanceof NewsCategory ? $article->category->value : 'official';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title.uz' => 'required|string|max:255',
            'body.uz' => 'required|string',
        ], attributes: [
            'title.uz' => __('Sarlavha (uz)'),
            'body.uz' => __('Matn (uz)'),
        ]);

        $data = [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'category' => NewsCategory::tryFrom($this->category),
            'published_at' => $this->published ? now() : null,
            'author_id' => auth()->id(),
        ];

        if ($this->editingId !== null) {
            NewsArticle::query()->findOrFail($this->editingId)->update($data);
        } else {
            $data['slug'] = Str::slug($this->title['uz']).'-'.Str::random(5);
            NewsArticle::query()->create($data);
        }

        $this->showForm = false;
        session()->flash('status', __('Saqlandi.'));
    }

    public function delete(int $id): void
    {
        NewsArticle::query()->findOrFail($id)->delete();
        session()->flash('status', __('O‘chirildi.'));
    }

    public function render(): View
    {
        return view('livewire.news-manager', [
            'articles' => NewsArticle::query()->latest('published_at')->paginate(10),
        ]);
    }
}
