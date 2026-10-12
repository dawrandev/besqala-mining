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
        <section class="relative z-10 mx-auto -mt-10 max-w-5xl px-4 sm:px-6 lg:px-8">
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

    {{-- Mission & strategic task --}}
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="card p-7">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400"><x-icon name="chart" class="h-6 w-6" /></span>
                <h3 class="mt-5 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Missiya') }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ __('Qayta tiklanuvchi energiya manbalaridan foydalangan holda, davlat nazorati ostidagi shaffof kripto-mayning ekotizimini rivojlantirish.') }}</p>
            </div>
            <div class="card p-7">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400"><x-icon name="eye" class="h-6 w-6" /></span>
                <h3 class="mt-5 text-lg font-semibold text-gray-900 dark:text-white">{{ __('Strategik vazifa') }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ __('Markaziy Osiyoda qonuniy va barqaror kripto-mayning bo‘yicha yetakchi hududga aylanish.') }}</p>
            </div>
        </div>
    </section>

    {{-- Values --}}
    <section class="mx-auto max-w-5xl px-4 pb-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Qadriyatlarimiz') }}</h2>
        <div class="mt-6 grid gap-5 md:grid-cols-2">
            @foreach ([
                [__('Shaffoflik'), __('Barcha jarayonlar elektron shaklda, QR-kodli hujjatlar va ochiq reestr orqali.')],
                [__('Yashil energiya'), __('Faoliyat qayta tiklanuvchi va muqobil energiya manbalariga asoslanadi.')],
                [__('Qonuniylik'), __('Kripto-aktivlar aylanmasi qonunchiligiga to‘liq rioya etiladi.')],
                [__('Tezkorlik'), __('Ariza 30 kun ichida ko‘rib chiqiladi, ruxsatnoma yig‘imsiz beriladi.')],
            ] as [$title, $desc])
                <div class="card flex gap-4 p-6">
                    <x-icon name="verify" class="mt-0.5 h-6 w-6 shrink-0 text-success-500" />
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- History timeline --}}
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Tarix') }}</h2>
        <div class="card mt-6 divide-y divide-gray-100 p-2 dark:divide-gray-800">
            @foreach ([
                ['2026', __('PQ–143-son qaror asosida maxsus zona tashkil etildi.')],
                ['2026', __('Rezidentlarni ro‘yxatga olish tartibi tasdiqlandi.')],
                ['2026', __('Birinchi rezidentlar faoliyatni boshladi.')],
            ] as [$year, $text])
                <div class="flex items-center gap-4 p-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent-500 text-sm font-bold text-white">{{ $year }}</span>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Contact --}}
    <section class="mx-auto max-w-5xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="card grid gap-6 p-7 sm:grid-cols-3">
            <div class="flex items-start gap-3">
                <x-icon name="building" class="mt-0.5 h-5 w-5 shrink-0 text-accent-500" />
                <div>
                    <p class="text-xs font-medium text-gray-400">{{ __('Manzil') }}</p>
                    <p class="text-sm text-gray-800 dark:text-gray-200">{{ __('Qoraqalpog‘iston Respublikasi, Nukus shahri, Do‘slik ko‘chasi 1') }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <x-icon name="news" class="mt-0.5 h-5 w-5 shrink-0 text-accent-500" />
                <div>
                    <p class="text-xs font-medium text-gray-400">{{ __('Email') }}</p>
                    <a href="mailto:info@besqala.uz" class="text-sm text-gray-800 hover:text-accent-600 dark:text-gray-200">info@besqala.uz</a>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <x-icon name="user" class="mt-0.5 h-5 w-5 shrink-0 text-accent-500" />
                <div>
                    <p class="text-xs font-medium text-gray-400">{{ __('Telefon') }}</p>
                    <a href="tel:+998612200000" class="text-sm text-gray-800 hover:text-accent-600 dark:text-gray-200">+998 61 220 00 00</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
