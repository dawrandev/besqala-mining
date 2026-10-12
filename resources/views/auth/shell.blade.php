<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Besqala Mining Valley — Maxsus mayning zonasi') }}</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-950">
<div class="flex min-h-screen flex-col lg:flex-row">
    <!-- Branding panel -->
    <div class="relative hidden overflow-hidden bg-gray-950 lg:flex lg:w-1/2">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-gray-950 to-gray-950"></div>
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute -right-24 top-1/3 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative z-10 flex w-full flex-col justify-between p-12">
            <x-logo :dark="true" />
            <div class="max-w-md">
                <h2 class="text-4xl font-bold leading-tight text-white">
                    {{ __('Maxsus mayning zonasi') }}
                    <span class="text-brand-400">{{ __('boshqaruv tizimi') }}</span>
                </h2>
                <p class="mt-4 text-base leading-relaxed text-gray-400">
                    {{ __('Rezidentlarni ro‘yxatdan o‘tkazish, ruxsatnomalar berish va faoliyatni nazorat qilishning yagona platformasi.') }}
                </p>
            </div>
            <p class="text-xs text-gray-500">{{ __('O‘zbekiston Respublikasi Istiqbolli loyihalar milliy agentligi') }}</p>
        </div>
    </div>

    <!-- Form panel -->
    <div class="flex flex-1 flex-col items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">
            <div class="mb-6 lg:hidden"><x-logo /></div>
            <div class="card p-6 sm:p-8">
                @yield('auth')
            </div>
            <a href="{{ route('registry') }}" class="mt-6 flex items-center justify-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">
                {{ __('Ochiq reestrni ko‘rish') }} <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>
    </div>
</div>
</body>
</html>
