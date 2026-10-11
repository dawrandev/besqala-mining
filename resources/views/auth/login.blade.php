@extends('auth.shell')

@section('auth')
    <div x-data="{
        tab: 'login',
        email: '',
        password: '',
        fill(mail) { this.email = mail; this.password = 'demo1234'; }
    }">
        <div class="mb-6 grid grid-cols-2 gap-1 rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
            <button type="button" @click="tab = 'login'"
                :class="tab === 'login' ? 'bg-white text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'"
                class="rounded-lg py-2 text-sm font-semibold transition">{{ __('Kirish') }}</button>
            <a href="{{ route('register') }}"
                class="rounded-lg py-2 text-center text-sm font-semibold text-gray-500 transition hover:text-gray-700">{{ __('Ro‘yxatdan o‘tish') }}</a>
        </div>

        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Tizimga kirish') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Email va parolingizni kiriting') }}</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }} <span class="text-error-500">*</span></label>
                <input type="email" name="email" x-model="email" required autofocus placeholder="email@example.uz"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @error('email') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Parol') }} <span class="text-error-500">*</span></label>
                <input type="password" name="password" x-model="password" required placeholder="••••••••"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-500 py-3 text-sm font-semibold text-white shadow-theme-sm transition hover:bg-brand-600">
                <x-icon name="logout" class="h-4 w-4 rotate-180" />
                {{ __('Kirish') }}
            </button>
        </form>

        <div class="mt-6">
            <p class="text-center text-xs font-medium uppercase tracking-wide text-gray-400">{{ __('Demo hisoblar (rolni sinab ko‘ring)') }}</p>
            <div class="mt-3 grid grid-cols-2 gap-2">
                @foreach ([
                    ['aral@besqala.uz', 'T', __('Talabgor'), 'Azamat Qalbaev'],
                    ['direksiya@besqala.uz', 'D', __('Direksiya'), 'Dilnoza Yusupova'],
                    ['expert@besqala.uz', 'E', __('Ekspert'), 'Rustam Nurmuxamedov'],
                    ['authority@besqala.uz', 'A', __('Agentlik'), 'Dmitriy Li'],
                    ['admin@besqala.uz', 'A', __('Admin'), __('Kontent administratori')],
                ] as [$mail, $ini, $roleLabel, $person])
                    <button type="button" @click="fill('{{ $mail }}')"
                        class="flex items-center gap-2 rounded-lg border border-gray-200 p-2.5 text-left transition hover:border-brand-300 hover:bg-brand-25 dark:border-gray-700 dark:hover:border-brand-500/40">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-brand-50 text-xs font-bold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $ini }}</span>
                        <span class="min-w-0 leading-tight">
                            <span class="block truncate text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $roleLabel }}</span>
                            <span class="block truncate text-[11px] text-gray-400">{{ $person }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
            <p class="mt-3 text-center text-xs text-gray-400">
                <x-icon name="shield" class="mr-1 inline h-3.5 w-3.5" />
                {{ __('Parol') }}: <code class="rounded bg-gray-100 px-1.5 py-0.5 font-mono dark:bg-gray-800">demo1234</code>
            </p>
        </div>
    </div>
@endsection
