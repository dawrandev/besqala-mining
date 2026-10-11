@extends('auth.shell')

@section('auth')
    <div>
        <div class="mb-6 grid grid-cols-2 gap-1 rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
            <a href="{{ route('login') }}"
                class="rounded-lg py-2 text-center text-sm font-semibold text-gray-500 transition hover:text-gray-700">{{ __('Kirish') }}</a>
            <span class="rounded-lg bg-white py-2 text-center text-sm font-semibold text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white">{{ __('Ro‘yxatdan o‘tish') }}</span>
        </div>

        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Yangi hisob yaratish') }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Rezident sifatida ro‘yxatdan o‘ting') }}</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('F.I.Sh.') }} <span class="text-error-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @error('name') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }} <span class="text-error-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@example.uz"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @error('email') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Telefon') }}</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+998 __ ___ __ __"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Parol') }} <span class="text-error-500">*</span></label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                @error('password') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Parolni tasdiqlang') }} <span class="text-error-500">*</span></label>
                <input type="password" name="password_confirmation" required placeholder="••••••••"
                    class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <button type="submit" class="w-full rounded-lg bg-brand-500 py-3 text-sm font-semibold text-white shadow-theme-sm transition hover:bg-brand-600">
                {{ __('Ro‘yxatdan o‘tish') }}
            </button>
        </form>
    </div>
@endsection
