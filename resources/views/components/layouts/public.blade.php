<!doctype html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('Besqala Mining Valley — maxsus mayning zonasi rezidentlarini ro‘yxatga olish va boshqarish platformasi') }}">
    <title>{{ $title ?? __('Besqala Mining Valley — Maxsus mayning zonasi') }}</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/80 backdrop-blur-lg dark:border-gray-800 dark:bg-gray-950/80">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <x-logo />

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ([
                    ['home', __('Bosh sahifa')],
                    ['about', __('Biz haqimizda')],
                    ['news.index', __('Yangiliklar')],
                    ['leadership', __('Rahbariyat')],
                    ['registry', __('Reestr')],
                    ['verify', __('Tekshirish')],
                ] as [$route, $label])
                    <a href="{{ route($route) }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium transition-colors
                            {{ request()->routeIs($route) ? 'text-accent-600 dark:text-accent-400' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <x-lang-switcher class="hidden sm:inline-flex" />
                @auth
                    <a href="{{ route('app.dashboard') }}" class="hidden rounded-lg bg-accent-500 px-4 py-2 text-sm font-semibold text-white shadow-theme-sm transition hover:bg-accent-600 sm:inline-block">{{ __('Kabinet') }}</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg bg-accent-500 px-4 py-2 text-sm font-semibold text-white shadow-theme-sm transition hover:bg-accent-600 sm:inline-block">{{ __('Kirish') }}</a>
                @endauth
                <button @click="open = !open" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 lg:hidden dark:text-gray-300 dark:hover:bg-white/5" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div x-show="open" x-cloak x-transition class="border-t border-gray-200 lg:hidden dark:border-gray-800">
            <nav class="space-y-1 px-4 py-3">
                @foreach ([
                    ['home', __('Bosh sahifa')], ['about', __('Biz haqimizda')], ['news.index', __('Yangiliklar')],
                    ['leadership', __('Rahbariyat')], ['registry', __('Reestr')], ['verify', __('Tekshirish')],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($route) ? 'bg-accent-50 text-accent-700 dark:bg-accent-500/15 dark:text-accent-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">{{ $label }}</a>
                @endforeach
                <div class="flex items-center justify-between pt-2">
                    <x-lang-switcher />
                    <a href="{{ auth()->check() ? route('app.dashboard') : route('login') }}" class="rounded-lg bg-accent-500 px-4 py-2 text-sm font-semibold text-white">{{ auth()->check() ? __('Kabinet') : __('Kirish') }}</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-24 border-t border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col items-start justify-between gap-8 md:flex-row">
                <div class="max-w-sm">
                    <x-logo />
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Qoraqalpog‘iston Respublikasida tashkil etilgan maxsus mayning zonasi. 2026-yil 17-apreldagi PQ–143-son qaror asosida.') }}</p>
                </div>
                <div class="grid grid-cols-2 gap-8 text-sm sm:grid-cols-2">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Bo‘limlar') }}</p>
                        <ul class="mt-3 space-y-2">
                            <li><a href="{{ route('registry') }}" class="text-gray-500 hover:text-accent-600 dark:text-gray-400">{{ __('Ochiq reestr') }}</a></li>
                            <li><a href="{{ route('verify') }}" class="text-gray-500 hover:text-accent-600 dark:text-gray-400">{{ __('Hujjatni tekshirish') }}</a></li>
                            <li><a href="{{ route('news.index') }}" class="text-gray-500 hover:text-accent-600 dark:text-gray-400">{{ __('Yangiliklar') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Tashkilot') }}</p>
                        <ul class="mt-3 space-y-2">
                            <li><a href="{{ route('about') }}" class="text-gray-500 hover:text-accent-600 dark:text-gray-400">{{ __('Biz haqimizda') }}</a></li>
                            <li><a href="{{ route('leadership') }}" class="text-gray-500 hover:text-accent-600 dark:text-gray-400">{{ __('Rahbariyat') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t border-gray-200 pt-6 text-xs text-gray-400 dark:border-gray-800">
                © {{ date('Y') }} Besqala Mining Valley. {{ __('O‘zbekiston Respublikasi Istiqbolli loyihalar milliy agentligi') }}.
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
