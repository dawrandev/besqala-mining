<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Concerns\HasTranslations;
use Database\Factories\NewsArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $slug
 * @property array<string, string> $title
 * @property array<string, string>|null $excerpt
 * @property array<string, string> $body
 * @property string|null $cover_image
 * @property \Illuminate\Support\Carbon|null $published_at
 */
#[Fillable(['slug', 'title', 'excerpt', 'body', 'cover_image', 'author_id', 'published_at'])]
class NewsArticle extends Model
{
    /** @use HasFactory<NewsArticleFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'excerpt' => 'array',
            'body' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
