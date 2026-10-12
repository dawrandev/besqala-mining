<x-layouts.public>
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Rezidentlar reestri') }}</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('«Besqala Mining Valley» maxsus zonasi rezidentlarining ochiq ma‘lumotlari.') }}</p>
        </div>

        <form method="GET" class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Nom, STIR yoki guvohnoma...') }}"
                    class="w-full rounded-xl border-gray-300 bg-white pl-10 text-sm shadow-theme-xs focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            </div>
            <select name="region" onchange="this.form.submit()"
                class="rounded-xl border-gray-300 bg-white text-sm shadow-theme-xs focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="">{{ __('Barcha hududlar') }}</option>
                @foreach (\App\Enums\Region::options() as $value => $label)
                    <option value="{{ $value }}" @selected($region === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-accent-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-accent-600">{{ __('Qidirish') }}</button>
        </form>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($residents as $resident)
                @php $cert = $resident->documents->first(); @endphp
                <div class="card p-6">
                    <div class="flex items-start justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400">
                            <x-icon name="building" />
                        </span>
                        <x-ui.badge :color="$resident->status->color()">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $resident->status->label() }}
                        </x-ui.badge>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">{{ $resident->company_name }}</h3>
                    <p class="text-xs text-gray-400">{{ __('STIR') }}: {{ $resident->stir }}</p>
                    <div class="mt-3 space-y-1 text-sm text-gray-500 dark:text-gray-400">
                        <p class="flex items-center gap-1.5"><x-icon name="registry" class="h-4 w-4 text-gray-400" /> {{ $resident->region->label() }}</p>
                        <p class="text-xs">{{ $resident->legal_form }}</p>
                    </div>
                    @if($cert)
                        <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                            <div>
                                <p class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $cert->number }}</p>
                                <p class="text-xs text-gray-400">{{ __('Amal qiladi') }}: {{ $cert->expires_at?->translatedFormat('d M Y') }}</p>
                            </div>
                            <a href="{{ route('verify', ['no' => $cert->number]) }}" class="text-gray-400 hover:text-accent-600"><x-icon name="arrow-right" class="h-4 w-4" /></a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="card col-span-full p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Hech narsa topilmadi.') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.public>
