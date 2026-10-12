<x-layouts.public>
    @php
        $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : $arr;
        $regionCount = count(\App\Enums\Region::cases());
    @endphp

    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden bg-gray-950">
        {{-- gradient mesh --}}
        <div class="absolute inset-0 bg-[radial-gradient(60%_80%_at_15%_0%,#0e815940_0%,transparent_60%),radial-gradient(50%_70%_at_100%_20%,#1aa06d33_0%,transparent_55%),linear-gradient(180deg,#050b0a,#050b0a)]"></div>
        <div class="absolute inset-0 opacity-[0.05]" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 48px 48px;"></div>
        <div class="absolute -top-24 -right-24 h-[34rem] w-[34rem] animate-pulse rounded-full bg-accent-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 -left-20 h-80 w-80 rounded-full bg-accent-700/20 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-12 lg:gap-8 lg:px-8 lg:py-28">
            <div class="lg:col-span-7">
                <span class="inline-flex items-center gap-2 rounded-full border border-accent-400/30 bg-accent-400/10 px-3.5 py-1.5 text-sm font-medium text-accent-200">
                    <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-accent-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-accent-400"></span></span>
                    {{ __('Qoraqalpog‘istonda tashkil etilgan maxsus mayning zonasi') }}
                </span>

                <h1 class="mt-7 text-4xl font-bold leading-[1.05] tracking-tight text-white sm:text-6xl lg:text-7xl">
                    Besqala
                    <span class="bg-gradient-to-r from-accent-300 via-accent-400 to-emerald-300 bg-clip-text text-transparent">Mining Valley</span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-gray-300">
                    {{ __('Qoraqalpog‘istonda tashkil etilgan maxsus mayning zonasi — kripto-aktivlar mayningini qonuniy, shaffof va ekologik toza energiya asosida amalga oshirish uchun yagona raqamli platforma.') }}
                </p>

                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="group inline-flex items-center gap-2 rounded-xl bg-accent-500 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-accent-500/25 transition hover:bg-accent-400">
                        {{ __('Rezident bo‘lish') }} <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" />
                    </a>
                    <a href="{{ route('registry') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10">
                        <x-icon name="registry" class="h-4 w-4" /> {{ __('Ochiq reestr') }}
                    </a>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm text-gray-400">
                    <span class="inline-flex items-center gap-2"><x-icon name="shield" class="h-4 w-4 text-accent-400" /> {{ __('Qonuniy maydon') }}</span>
                    <span class="inline-flex items-center gap-2"><x-icon name="chart" class="h-4 w-4 text-accent-400" /> {{ __('Toza energiya') }}</span>
                    <span class="inline-flex items-center gap-2"><x-icon name="verify" class="h-4 w-4 text-accent-400" /> {{ __('QR-tekshiruv') }}</span>
                </div>

                {{-- Service value stats --}}
                <dl class="mt-10 grid max-w-xl grid-cols-2 gap-px overflow-hidden rounded-2xl border border-white/10 bg-white/10 sm:grid-cols-4">
                    @foreach ([
                        ['5', __('yil'), __('Ruxsatnoma muddati')],
                        ['30', __('kun'), __('Ariza ko‘rib chiqish')],
                        ['0', __('so‘m'), __('Yig‘imsiz')],
                        ['100%', '', __('Elektron xizmat')],
                    ] as [$num, $unit, $cap])
                        <div class="bg-gray-950/80 p-4">
                            <dt class="text-2xl font-bold text-white">{{ $num }}@if($unit)<span class="text-base font-medium text-accent-300"> {{ $unit }}</span>@endif</dt>
                            <dd class="mt-0.5 text-xs text-gray-400">{{ $cap }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Floating permit card visual --}}
            <div class="hidden lg:col-span-5 lg:block">
                <div class="relative mx-auto max-w-sm">
                    <div class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-accent-400/20 to-transparent blur-2xl"></div>
                    <div class="relative rotate-1 rounded-3xl border border-white/10 bg-white/[0.06] p-6 shadow-2xl backdrop-blur-xl transition hover:rotate-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-accent-400 to-accent-600 text-white"><x-icon name="bitcoin" class="h-5 w-5" /></span>
                                <span class="text-sm font-semibold text-white">Besqala Mining</span>
                            </div>
                            <span class="rounded-full bg-accent-400/15 px-2.5 py-0.5 text-xs font-medium text-accent-300">{{ __('Amal qiladi') }}</span>
                        </div>
                        <p class="mt-6 text-xs text-gray-400">{{ __('Guvohnoma') ?? 'Guvohnoma' }}</p>
                        <p class="font-mono text-2xl font-bold tracking-wider text-white">G-2026-0001</p>
                        <div class="mt-5 grid grid-cols-2 gap-4 border-t border-white/10 pt-5 text-sm">
                            <div><p class="text-xs text-gray-500">{{ __('Quvvat') }}</p><p class="font-semibold text-white">24 MW</p></div>
                            <div><p class="text-xs text-gray-500">{{ __('Energiya manbai') }}</p><p class="font-semibold text-white">{{ __('Quyosh / Gidro') }}</p></div>
                        </div>
                        <div class="mt-5 flex items-center gap-3 rounded-xl bg-white/5 p-3">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-white"><x-icon name="verify" class="h-7 w-7 text-gray-900" /></span>
                            <p class="text-xs leading-relaxed text-gray-400">{{ __('Har bir guvohnoma va ruxsatnoma raqam hamda QR-kod orqali tekshiriladi.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== STATS BAND ===================== --}}
    <section class="relative z-10 -mt-14 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                [__('Rezidentlar'), $residentCount, 'building'],
                [__('Faol rezidentlar'), $activeCount, 'check-doc'],
                [__('Umumiy quvvat'), number_format($totalPower, 0).' MW', 'chart'],
                [__('Qazib olingan'), number_format($minedTotal, 2).' BTC', 'bitcoin'],
            ] as [$label, $value, $icon])
                <div class="card p-6 transition hover:-translate-y-1 hover:shadow-theme-lg">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400">
                        <x-icon :name="$icon" />
                    </span>
                    <p class="mt-4 text-2xl font-bold text-gray-900 lg:text-3xl dark:text-white">{{ $value }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== FEATURES ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-sm font-semibold tracking-wide text-accent-600 uppercase dark:text-accent-400">{{ __('Nega Besqala Mining Valley?') }}</span>
            <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">{{ __('To‘liq elektron, shaffof va QR-kodli hujjatlar asosidagi zamonaviy boshqaruv.') }}</h2>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['shield', __('Qonuniy maydon'), __('Davlat tomonidan tartibga solinadigan maxsus zona doirasida kafolatli faoliyat.')],
                ['chart', __('Toza energiya'), __('Qayta tiklanuvchi energiya manbalariga asoslangan ekologik mayning.')],
                ['verify', __('QR-tekshiruv'), __('Har bir guvohnoma va ruxsatnoma raqam hamda QR-kod orqali tekshiriladi.')],
            ] as [$icon, $title, $desc])
                <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-7 transition hover:border-accent-300 hover:shadow-theme-lg dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-accent-500/40">
                    <div class="absolute -top-10 -right-10 h-28 w-28 rounded-full bg-accent-500/5 transition group-hover:scale-150"></div>
                    <span class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-accent-500 to-accent-700 text-white shadow-theme-sm">
                        <x-icon :name="$icon" class="h-6 w-6" />
                    </span>
                    <h3 class="relative mt-5 text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                    <p class="relative mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== PROCESS ===================== --}}
    <section class="border-y border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-sm font-semibold tracking-wide text-accent-600 uppercase dark:text-accent-400">{{ __('Rezidentlikka ariza topshirish') }}</span>
                <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">{{ __('Rezidentlarni ro‘yxatdan o‘tkazish, ruxsatnomalar berish va faoliyatni nazorat qilishning yagona platformasi.') }}</h2>
            </div>
            <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['doc', __('Ariza topshirish'), __('Yuridik shaxslar rezident sifatida ro‘yxatdan o‘tib faoliyat yuritadilar.')],
                    ['check-doc', __('Ekspert xulosasi'), __('Arizalarni ko‘rib chiqish va xulosa tayyorlash')],
                    ['shield', __('Ruxsatnoma berish'), __('Ruxsatnomalar berish va nazoratni amalga oshirish')],
                    ['building', __('Faol rezidentlik'), __('Ro‘yxatga olingan rezident korxonalar')],
                ] as $i => [$icon, $title, $desc])
                    <div class="relative">
                        <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                            <div class="flex items-center justify-between">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400"><x-icon :name="$icon" /></span>
                                <span class="text-3xl font-bold text-gray-100 dark:text-gray-800">0{{ $i + 1 }}</span>
                            </div>
                            <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== ALLOWED ENERGY TYPES ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-accent-50 px-3 py-1 text-sm font-semibold text-accent-700 dark:bg-accent-500/15 dark:text-accent-400">
                    <x-icon name="chart" class="h-4 w-4" /> {{ __('Energiya manbalari') }}
                </span>
                <h2 class="mt-4 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">{{ __('Ruxsat etilgan energiya turlari') }}</h2>
                <p class="mt-4 text-lg leading-relaxed text-gray-500 dark:text-gray-400">{{ __('Mayning faoliyati faqat Nizomda belgilangan energiya manbalaridan foydalangan holda amalga oshiriladi. Iste‘mol qilinadigan elektr energiyasi alohida hisobga olinadi.') }}</p>
                <a href="{{ route('about') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-accent-600 hover:text-accent-700 dark:text-accent-400">{{ __('Batafsil ma‘lumot') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['sun', __('Qayta tiklanuvchi'), __('Quyosh va shamol energiyasi manbalari')],
                    ['bitcoin', __('Vodorod'), __('Vodorod elektr stansiyalari')],
                    ['chart', __('Yagona tizim'), __('Yagona elektr energetika tizimi')],
                    ['building', __('Uglevodorod'), __('Kam debitli va eskirgan konlar')],
                ] as [$icon, $title, $desc])
                    <div class="card p-6">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400"><x-icon :name="$icon" /></span>
                        <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== OBLIGATIONS vs PROHIBITED ===================== --}}
    <section class="border-y border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
            <div class="rounded-2xl border border-success-200 bg-white p-7 dark:border-success-500/20 dark:bg-white/[0.03]">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500"><x-icon name="check-doc" /></span>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Rezident majburiyatlari') }}</h3>
                </div>
                <ul class="mt-5 space-y-3">
                    @foreach ([
                        __('Faqat ko‘rsatilgan manzilda mayning amalga oshirish'),
                        __('Topilgan kripto-aktivlarni hisobga olib borish'),
                        __('Kripto-aktivlar aylanmasi qonunchiligiga rioya qilish'),
                        __('Har oyda faoliyat hisobotini taqdim etish'),
                        __('Belgilangan ajratmalarni o‘z vaqtida to‘lash'),
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <x-icon name="verify" class="mt-0.5 h-4 w-4 shrink-0 text-success-500" /> {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-2xl border border-error-200 bg-white p-7 dark:border-error-500/20 dark:bg-white/[0.03]">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500"><x-icon name="alert" /></span>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Taqiqlangan faoliyat') }}</h3>
                </div>
                <ul class="mt-5 space-y-3">
                    @foreach ([
                        __('Yashirin mayning (yashirin dasturiy ta‘minot orqali)'),
                        __('Anonim kripto-aktivlar mayningi'),
                        __('Muomaladan chiqarilgan kripto-aktivlarni chiqarish'),
                        __('Ko‘rsatilmagan manzilda mayning qilish'),
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-error-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="m5.6 5.6 12.8 12.8"/></svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ===================== LEGAL / COVERAGE BAND ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div>
                <span class="text-sm font-semibold tracking-wide text-accent-600 uppercase dark:text-accent-400">{{ __('Huquqiy asos') }}</span>
                <h2 class="mt-3 text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white">{{ __('Maxsus mayning zonasi') }}</h2>
                <p class="mt-4 text-lg leading-relaxed text-gray-500 dark:text-gray-400">{{ __('Zona 2026-yil 17-apreldagi PQ–143-son qaror asosida tashkil etilgan.') }}</p>
                <dl class="mt-8 grid grid-cols-3 gap-4">
                    @foreach ([
                        [$regionCount, __('Hudud')],
                        ['24/7', __('QR-tekshiruv')],
                        ['100%', __('Shaffoflik')],
                    ] as [$num, $cap])
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                            <p class="text-2xl font-bold text-accent-600 dark:text-accent-400">{{ $num }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $cap }}</p>
                        </div>
                    @endforeach
                </dl>
                <a href="{{ route('about') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-accent-600 hover:text-accent-700 dark:text-accent-400">{{ __('Biz haqimizda') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
            </div>
            <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-gradient-to-br from-accent-500 to-accent-700 p-8 text-white shadow-theme-lg dark:border-gray-800">
                <div class="absolute -top-16 -right-16 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
                <x-icon name="shield" class="h-10 w-10 text-white/90" />
                <p class="mt-5 text-lg leading-relaxed font-medium">{{ __('Davlat tomonidan tartibga solinadigan maxsus zona doirasida kafolatli faoliyat.') }}</p>
                <p class="mt-6 text-sm text-white/70">{{ __('O‘zbekiston Respublikasi Istiqbolli loyihalar milliy agentligi') }}</p>
            </div>
        </div>
    </section>

    {{-- ===================== LATEST NEWS ===================== --}}
    @if($latestNews->isNotEmpty())
        <section class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-sm font-semibold tracking-wide text-accent-600 uppercase dark:text-accent-400">{{ __('Yangiliklar') }}</span>
                        <h2 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ __('So‘nggi yangiliklar') }}</h2>
                    </div>
                    <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-accent-600 hover:text-accent-700 dark:text-accent-400">{{ __('Barchasi') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($latestNews as $article)
                        <a href="{{ route('news.show', $article->slug) }}" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:-translate-y-1 hover:shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
                            <div class="relative flex h-44 items-center justify-center overflow-hidden bg-gradient-to-br from-accent-500 to-accent-700">
                                <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 28px 28px;"></div>
                                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/10"></div>
                                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-white/15 backdrop-blur transition group-hover:scale-110">
                                    <x-icon name="news" class="h-7 w-7 text-white" />
                                </span>
                            </div>
                            <div class="p-6">
                                <p class="text-xs font-medium text-gray-400">{{ $article->published_at?->translatedFormat('d F Y') }}</p>
                                <h3 class="mt-2 line-clamp-2 font-semibold text-gray-900 group-hover:text-accent-600 dark:text-white dark:group-hover:text-accent-400">{{ $t($article->title) }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $t($article->excerpt) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== CTA ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gray-950 px-8 py-14 text-center sm:px-16 sm:py-20">
            <div class="absolute inset-0 bg-[radial-gradient(60%_120%_at_50%_0%,#0e815955_0%,transparent_60%)]"></div>
            <div class="absolute -bottom-24 left-1/2 h-64 w-[40rem] -translate-x-1/2 rounded-full bg-accent-500/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-2xl">
                <h2 class="text-3xl font-bold text-white sm:text-4xl">{{ __('Rezident sifatida ro‘yxatdan o‘ting') }}</h2>
                <p class="mt-4 text-lg text-gray-300">{{ __('Rezidentlikka ariza topshirish') }}</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-accent-500 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-accent-500/25 transition hover:bg-accent-400">{{ __('Rezident bo‘lish') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                    <a href="{{ route('verify') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-7 py-3.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10">{{ __('Hujjatni tekshirish') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
