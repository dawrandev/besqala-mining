<x-layouts.public>
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : $arr; @endphp
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Yangiliklar') }}</h1>
        <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Zona faoliyati va rezidentlarga oid so‘nggi xabarlar.') }}</p>

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($articles as $article)
                <a href="{{ route('news.show', $article->slug) }}" class="card group overflow-hidden transition hover:-translate-y-1 hover:shadow-theme-lg">
                    <div class="relative flex h-44 items-center justify-center overflow-hidden bg-gradient-to-br from-accent-500 to-accent-700">
                        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 28px 28px;"></div>
                        <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/10"></div>
                        <span class="grid h-14 w-14 place-items-center rounded-2xl bg-white/15 backdrop-blur transition group-hover:scale-110">
                            <x-icon name="news" class="h-7 w-7 text-white" />
                        </span>
                        <span class="absolute left-4 top-4 rounded-full bg-white/15 px-2.5 py-0.5 text-xs font-medium text-white backdrop-blur">{{ __('Yangilik') ?? 'Yangilik' }}</span>
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-gray-400">{{ $article->published_at?->translatedFormat('d F Y') }}</p>
                        <h2 class="mt-2 line-clamp-2 text-lg font-semibold text-gray-900 group-hover:text-accent-600 dark:text-white">{{ $t($article->title) }}</h2>
                        <p class="mt-2 line-clamp-3 text-sm text-gray-500 dark:text-gray-400">{{ $t($article->excerpt) }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-accent-600 dark:text-accent-400">{{ __('Batafsil') }} <x-icon name="arrow-right" class="h-4 w-4" /></span>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-gray-500">{{ __('Yangiliklar yo‘q.') }}</p>
            @endforelse
        </div>

        <div class="mt-10">{{ $articles->links() }}</div>
    </section>
</x-layouts.public>
