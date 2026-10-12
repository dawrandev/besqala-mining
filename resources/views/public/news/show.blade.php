<x-layouts.public>
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : $arr; @endphp
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-accent-600">
            <x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> {{ __('Yangiliklar') }}
        </a>
        <div class="mt-6 flex items-center gap-3">
            @if($article->category)
                <span class="rounded-full bg-accent-50 px-2.5 py-0.5 text-xs font-semibold text-accent-700 dark:bg-accent-500/15 dark:text-accent-400">{{ $article->category->label() }}</span>
            @endif
            <p class="text-sm text-gray-400">{{ $article->published_at?->translatedFormat('d F Y') }}</p>
        </div>
        <h1 class="mt-2 text-3xl font-bold leading-tight text-gray-900 dark:text-white sm:text-4xl">{{ $t($article->title) }}</h1>
        <div class="mt-6 flex h-56 items-center justify-center rounded-2xl bg-gradient-to-br from-accent-500/10 to-accent-700/10">
            <x-icon name="news" class="h-14 w-14 text-accent-400" />
        </div>
        <div class="prose prose-gray mt-8 max-w-none text-gray-700 dark:prose-invert dark:text-gray-300">
            @foreach (preg_split('/\n+/', $t($article->body)) as $para)
                <p class="mb-4 leading-relaxed">{{ $para }}</p>
            @endforeach
        </div>

        @if($related->isNotEmpty())
            <div class="mt-14 border-t border-gray-200 pt-8 dark:border-gray-800">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Boshqa yangiliklar') }}</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-3">
                    @foreach ($related as $item)
                        <a href="{{ route('news.show', $item->slug) }}" class="card p-5 transition hover:shadow-theme-md">
                            <p class="text-xs text-gray-400">{{ $item->published_at?->translatedFormat('d M Y') }}</p>
                            <h3 class="mt-1 line-clamp-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $t($item->title) }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</x-layouts.public>
