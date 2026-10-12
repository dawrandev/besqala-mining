<x-layouts.public>
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : ($arr ?? ''); @endphp
    <section class="bg-gray-950">
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-accent-950 via-gray-950 to-gray-950"></div>
            <div class="relative mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-white sm:text-4xl">{{ $about ? $t($about['title']) : __('Biz haqimizda') }}</h1>
                @if($about)
                    <p class="mx-auto mt-6 max-w-3xl text-lg leading-relaxed text-gray-300">{{ $t($about['intro']) }}</p>
                @endif
            </div>
        </div>
    </section>

    @if($about && !empty($about['stats']))
        <section class="mx-auto -mt-10 max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach ($about['stats'] as $stat)
                    <div class="card p-7 text-center">
                        <p class="text-3xl font-bold text-accent-600 dark:text-accent-400">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $t($stat['label']) }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ([
                ['shield', __('Huquqiy asos'), __('Zona 2026-yil 17-apreldagi PQ–143-son qaror asosida tashkil etilgan.')],
                ['chart', __('Ekologik mayning'), __('Faoliyat qayta tiklanuvchi va toza energiya manbalariga asoslanadi.')],
                ['verify', __('Shaffoflik'), __('Barcha hujjatlar elektron shaklda beriladi va QR-kod orqali tekshiriladi.')],
                ['building', __('Rezidentlik'), __('Yuridik shaxslar rezident sifatida ro‘yxatdan o‘tib faoliyat yuritadilar.')],
            ] as [$icon, $title, $desc])
                <div class="card flex gap-4 p-6">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400"><x-icon :name="$icon" /></span>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>
