@php
    $user = auth()->user();
    $role = $user->role;

    // [routeName, label, iconName]
    $primary = match ($role) {
        \App\Enums\UserRole::Applicant => [
            ['app.dashboard', __('Boshqaruv paneli'), 'grid'],
            ['app.applications', __('Mening arizalarim'), 'doc'],
            ['app.applications.create', __('Yangi ariza'), 'plus'],
            ['app.cabinet', __('Shaxsiy kabinet'), 'user'],
        ],
        \App\Enums\UserRole::Direksiya => [
            ['app.dashboard', __('Boshqaruv paneli'), 'grid'],
            ['app.queue', __('Arizalar navbati'), 'list'],
            ['app.residents', __('Rezidentlar'), 'building'],
            ['app.reports', __('Hisobotlar'), 'chart'],
        ],
        \App\Enums\UserRole::Expert => [
            ['app.dashboard', __('Boshqaruv paneli'), 'grid'],
            ['app.review', __('Ko‘rib chiqish'), 'check-doc'],
        ],
        \App\Enums\UserRole::Authority => [
            ['app.dashboard', __('Boshqaruv paneli'), 'grid'],
            ['app.permits', __('Ruxsatnomalar'), 'shield'],
            ['app.oversight', __('Nazorat'), 'eye'],
        ],
        \App\Enums\UserRole::Admin => [
            ['app.dashboard', __('Boshqaruv paneli'), 'grid'],
            ['app.admin.news', __('Yangiliklar'), 'news'],
            ['app.admin.leadership', __('Rahbariyat'), 'users'],
            ['app.admin.about', __('Biz haqimizda'), 'info'],
        ],
    };

    $public = [
        ['registry', __('Ochiq reestr'), 'registry'],
        ['verify', __('Hujjatni tekshirish'), 'verify'],
    ];

    $unread = $user->userNotifications()->whereNull('read_at')->count();
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
@endphp

<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($title ?? $role->panelTitle()) }} — Besqala Mining Valley</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-950">
<div x-data="{ sidebar: false, profile: false }" class="flex min-h-screen">
    <!-- Sidebar -->
    <aside x-cloak
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:static lg:translate-x-0 dark:border-gray-800 dark:bg-gray-900"
        :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center border-b border-gray-200 px-6 dark:border-gray-800">
            <x-logo />
        </div>
        <nav class="no-scrollbar flex-1 space-y-6 overflow-y-auto px-4 py-6">
            <div>
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $role->label() }}</p>
                <div class="space-y-1">
                    @foreach ($primary as [$route, $label, $icon])
                        <a href="{{ route($route) }}" class="menu-item {{ request()->routeIs($route) ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <x-icon :name="$icon" />
                            <span>{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Ommaviy') }}</p>
                <div class="space-y-1">
                    @foreach ($public as [$route, $label, $icon])
                        <a href="{{ route($route) }}" class="menu-item {{ request()->routeIs($route) ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <x-icon :name="$icon" />
                            <span>{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>
    </aside>

    <!-- Backdrop (mobile) -->
    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"></div>

    <!-- Main column -->
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-gray-200 bg-white/90 px-4 backdrop-blur sm:px-6 dark:border-gray-800 dark:bg-gray-900/90">
            <div class="flex items-center gap-3">
                <button @click="sidebar = true" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden dark:hover:bg-white/5">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Besqala Mining Valley</span>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <x-lang-switcher class="hidden sm:inline-flex" />
                <button @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5" aria-label="Theme">
                    <x-icon name="sun" class="hidden h-5 w-5 dark:block" />
                    <x-icon name="moon" class="h-5 w-5 dark:hidden" />
                </button>
                <a href="#" class="relative rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">
                    <x-icon name="bell" />
                    @if($unread > 0)
                        <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-error-500 px-1 text-[10px] font-bold text-white">{{ $unread }}</span>
                    @endif
                </a>

                <div class="relative">
                    <button @click="profile = !profile" class="flex items-center gap-2 rounded-lg p-1 pr-2 hover:bg-gray-100 dark:hover:bg-white/5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white">{{ $initials }}</span>
                        <span class="hidden text-left leading-tight sm:block">
                            <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }}</span>
                            <span class="block text-xs text-gray-400">{{ $role->label() }}</span>
                        </span>
                    </button>
                    <div x-show="profile" x-cloak @click.outside="profile = false" x-transition
                        class="absolute right-0 mt-2 w-56 rounded-xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-3 py-2 dark:border-gray-800">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }}</p>
                            <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="pt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10">
                                <x-icon name="logout" class="h-4 w-4" />
                                {{ __('Chiqish') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>
</div>
@livewireScripts
</body>
</html>
