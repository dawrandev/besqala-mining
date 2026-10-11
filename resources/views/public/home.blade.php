<x-layouts.public>
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : $arr; @endphp

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gray-950">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-gray-950 to-gray-950"></div>
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 44px 44px;"></div>
        <div class="absolute -right-32 -top-20 h-[32rem] w-[32rem] rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-brand-500/30 bg-brand-500/10 px-3 py-1 text-sm font-medium text-brand-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                    {{ __('PQ–143-son qaror asosida') }}
                </span>
                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    {{ __('Besqala') }} <span class="bg-gradient-to-r from-brand-400 to-brand-300 bg-clip-text text-transparent">Mining Valley</span>
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-gray-300">
                    {{ __('Qoraqalpog‘istonda tashkil etilgan maxsus mayning zonasi — kripto-aktivlar mayningini qonuniy, shaffof va ekologik toza energiya asosida amalga oshirish uchun yagona raqamli platforma.') }}
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-lg transition hover:bg-brand-600">
                        {{ __('Rezident bo‘lish') }} <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    <a href="{{ route('registry') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
                        {{ __('Ochiq reestr') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats band --}}
    <section class="relative -mt-12 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                [__('Rezidentlar'), $residentCount, 'building'],
                [__('Faol'), $activeCount, 'check-doc'],
                [__('Umumiy quvvat'), number_format($totalPower, 0).' MW', 'chart'],
                [__('Qazib olingan'), number_format($minedTotal, 2).' BTC', 'bitcoin'],
            ] as [$label, $value, $icon])
                <div class="card p-6">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                        <x-icon :name="$icon" />
                    </span>
                    <p class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Features --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Nega Besqala Mining Valley?') }}</h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">{{ __('To‘liq elektron, shaffof va QR-kodli hujjatlar asosidagi zamonaviy boshqaruv.') }}</p>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['shield', __('Qonuniy maydon'), __('Davlat tomonidan tartibga solinadigan maxsus zona doirasida kafolatli faoliyat.')],
                ['chart', __('Toza energiya'), __('Qayta tiklanuvchi energiya manbalariga asoslangan ekologik mayning.')],
                ['verify', __('QR-tekshiruv'), __('Har bir guvohnoma va ruxsatnoma raqam hamda QR-kod orqali tekshiriladi.')],
            ] as [$icon, $title, $desc])
                <div class="card p-7 transition hover:shadow-theme-md">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500 text-white shadow-theme-sm">
                        <x-icon :name="$icon" class="h-6 w-6" />
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Latest news --}}
    @if($latestNews->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('So‘nggi yangiliklar') }}</h2>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Barchasi') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach ($latestNews as $article)
                    <a href="{{ route('news.show', $article->slug) }}" class="card group overflow-hidden transition hover:shadow-theme-md">
                        <div class="flex h-40 items-center justify-center bg-gradient-to-br from-brand-500/10 to-brand-700/10">
                            <x-icon name="news" class="h-10 w-10 text-brand-400" />
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-gray-400">{{ $article->published_at?->translatedFormat('d F Y') }}</p>
                            <h3 class="mt-2 line-clamp-2 font-semibold text-gray-900 group-hover:text-brand-600 dark:text-white">{{ $t($article->title) }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">{{ $t($article->excerpt) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>
