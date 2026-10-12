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

    $notifications = $user->userNotifications()->latest()->limit(6)->get();
    $unread = $notifications->whereNull('read_at')->count();
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
<body class="font-outfit bg-gray-50 dark:bg-gray-900">
<div x-data="{ sidebarOpen: false, profile: false, notify: false }" class="xl:flex">

    {{-- ======================= SIDEBAR ======================= --}}
    <aside x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="sidebar fixed top-0 left-0 z-99999 flex h-screen w-[290px] flex-col overflow-y-auto border-r border-gray-200 bg-white px-5 transition-transform duration-300 xl:static xl:translate-x-0 dark:border-gray-800 dark:bg-black"
        @click.outside="if (window.innerWidth < 1280) sidebarOpen = false">

        <div class="flex items-center gap-2 pt-8 pb-7">
            <a href="{{ route('app.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-accent-500 to-accent-700 text-white shadow-theme-xs">
                    <x-icon name="bitcoin" class="h-5 w-5" />
                </span>
                <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Besqala<span class="text-accent-600 dark:text-accent-400">Mining</span></span>
            </a>
        </div>

        <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
            <nav>
                <div class="mb-6">
                    <h3 class="mb-4 text-xs leading-5 font-semibold tracking-wider text-gray-400 uppercase">{{ $role->label() }}</h3>
                    <ul class="flex flex-col gap-1">
                        @foreach ($primary as [$route, $label, $icon])
                            <li>
                                <a href="{{ route($route) }}" @class(['menu-item group', 'menu-item-active' => request()->routeIs($route), 'menu-item-inactive' => ! request()->routeIs($route)])>
                                    <x-icon :name="$icon" @class(['h-5 w-5', 'menu-item-icon-active' => request()->routeIs($route), 'menu-item-icon-inactive' => ! request()->routeIs($route)]) />
                                    <span class="menu-item-text">{{ $label }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="mb-4 text-xs leading-5 font-semibold tracking-wider text-gray-400 uppercase">{{ __('Ommaviy') }}</h3>
                    <ul class="flex flex-col gap-1">
                        @foreach ($public as [$route, $label, $icon])
                            <li>
                                <a href="{{ route($route) }}" @class(['menu-item group', 'menu-item-active' => request()->routeIs($route), 'menu-item-inactive' => ! request()->routeIs($route)])>
                                    <x-icon :name="$icon" @class(['h-5 w-5', 'menu-item-icon-active' => request()->routeIs($route), 'menu-item-icon-inactive' => ! request()->routeIs($route)]) />
                                    <span class="menu-item-text">{{ $label }}</span>
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('home') }}" class="menu-item menu-item-inactive group">
                                <x-icon name="arrow-right" class="menu-item-icon-inactive h-5 w-5" />
                                <span class="menu-item-text">{{ __('Bosh sahifa') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </aside>

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-9999 bg-gray-900/50 xl:hidden"></div>

    {{-- ======================= MAIN COLUMN ======================= --}}
    <div class="relative flex min-h-screen flex-1 flex-col">

        {{-- Header --}}
        <header class="sticky top-0 z-99 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex grow items-center justify-between gap-2 px-4 py-3 sm:gap-4 lg:px-6">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 xl:hidden dark:border-gray-800 dark:text-gray-400">
                        <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M3.25 6A.75.75 0 0 1 4 5.25h16a.75.75 0 0 1 0 1.5H4A.75.75 0 0 1 3.25 6Zm0 6a.75.75 0 0 1 .75-.75h16a.75.75 0 0 1 0 1.5H4a.75.75 0 0 1-.75-.75Zm0 6a.75.75 0 0 1 .75-.75h16a.75.75 0 0 1 0 1.5H4a.75.75 0 0 1-.75-.75Z"/></svg>
                    </button>

                    {{-- Search (desktop) --}}
                    <div class="hidden lg:block">
                        <div class="relative">
                            <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2">
                                <svg class="h-5 w-5 fill-gray-500 dark:fill-gray-400" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04 9.37a6.33 6.33 0 1 1 12.67 0 6.33 6.33 0 0 1-12.67 0ZM9.37 1.54a7.83 7.83 0 1 0 4.98 13.88l2.82 2.82a.75.75 0 1 0 1.06-1.06l-2.82-2.82A7.83 7.83 0 0 0 9.37 1.54Z"/></svg>
                            </span>
                            <input type="text" placeholder="{{ __('Qidirish') }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pr-4 pl-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 xl:w-80 dark:border-gray-800 dark:bg-gray-900 dark:text-white/90" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <x-lang-switcher class="hidden sm:inline-flex" />

                    {{-- Dark toggle --}}
                    <button @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                        class="nav-icon-item nav-icon-item-inactive" aria-label="Theme">
                        <x-icon name="sun" class="hidden h-5 w-5 dark:block" />
                        <x-icon name="moon" class="h-5 w-5 dark:hidden" />
                    </button>

                    {{-- Notifications --}}
                    <div class="relative">
                        <button @click="notify = !notify" class="nav-icon-item nav-icon-item-inactive relative">
                            @if($unread > 0)
                                <span class="absolute top-0.5 right-0.5 flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-error-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-error-500"></span></span>
                            @endif
                            <x-icon name="bell" class="h-5 w-5" />
                        </button>
                        <div x-show="notify" x-cloak @click.outside="notify = false" x-transition
                            class="absolute right-0 mt-3 flex w-80 flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
                            <div class="mb-2 flex items-center justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ __('Bildirishnomalar') ?? 'Bildirishnomalar' }}</h5>
                                @if($unread > 0)<span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $unread }}</span>@endif
                            </div>
                            <ul class="custom-scrollbar flex max-h-80 flex-col overflow-y-auto">
                                @forelse($notifications as $n)
                                    <li class="flex gap-3 rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-white/5">
                                        <span class="mt-1 flex h-2 w-2 shrink-0 rounded-full {{ $n->read_at ? 'bg-gray-300' : 'bg-brand-500' }}"></span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-gray-700 dark:text-gray-300">{{ $n->title }}</p>
                                            @if($n->body)<p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $n->body }}</p>@endif
                                            <p class="text-xs text-gray-400">{{ $n->created_at?->diffForHumans() }}</p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="py-6 text-center text-sm text-gray-400">{{ __('Ma‘lumot yo‘q.') }}</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    {{-- Profile --}}
                    <div class="relative">
                        <button @click="profile = !profile" class="flex items-center gap-2 rounded-lg p-1 pr-2 hover:bg-gray-100 dark:hover:bg-white/5">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white">{{ $initials }}</span>
                            <span class="hidden text-left leading-tight sm:block">
                                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }}</span>
                                <span class="block text-xs text-gray-400">{{ $role->label() }}</span>
                            </span>
                            <svg class="hidden h-4 w-4 fill-gray-500 sm:block" viewBox="0 0 20 20"><path d="M4.79 7.4 10 12.6l5.21-5.2" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div x-show="profile" x-cloak @click.outside="profile = false" x-transition
                            class="absolute right-0 mt-3 w-60 rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900">
                            <div class="border-b border-gray-100 pb-3 dark:border-gray-800">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                            <ul class="flex flex-col gap-1 border-b border-gray-100 py-2 dark:border-gray-800">
                                <li><a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5"><x-icon name="grid" class="h-4 w-4" /> {{ __('Bosh sahifa') }}</a></li>
                                <li><a href="{{ route('registry') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5"><x-icon name="registry" class="h-4 w-4" /> {{ __('Ochiq reestr') }}</a></li>
                            </ul>
                            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-medium text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10">
                                    <x-icon name="logout" class="h-4 w-4" /> {{ __('Chiqish') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-(--breakpoint-2xl) flex-1 p-4 md:p-6">
            {{ $slot }}
        </main>
    </div>
</div>
@livewireScripts
</body>
</html>
