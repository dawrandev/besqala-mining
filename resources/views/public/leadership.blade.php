<x-layouts.public>
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : ($arr ?? ''); @endphp
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Rahbariyat') }}</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Zona faoliyatini boshqaruvchi mas‘ul shaxslar.') }}</p>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($leaders as $leader)
                <div class="card p-6">
                    <div class="flex items-center gap-4">
                        <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-accent-500 to-accent-700 text-xl font-bold text-white">
                            {{ \Illuminate\Support\Str::of($t($leader->full_name))->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $t($leader->full_name) }}</h3>
                            <p class="text-sm text-accent-600 dark:text-accent-400">{{ $t($leader->position) }}</p>
                        </div>
                    </div>
                    @if($t($leader->department))
                        <p class="mt-4 text-xs font-medium text-gray-400">{{ $t($leader->department) }}</p>
                    @endif
                    @if($t($leader->bio))
                        <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $t($leader->bio) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>
