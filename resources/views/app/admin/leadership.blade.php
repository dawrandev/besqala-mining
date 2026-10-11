<x-layouts.app :title="__('Rahbariyat')">
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : $arr; @endphp
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Rahbariyat')" :subtitle="__('Rahbariyat a‘zolarini boshqarish')" />
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($leaders as $leader)
                <div class="card p-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">
                            {{ \Illuminate\Support\Str::of($t($leader->full_name))->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate font-semibold text-gray-900 dark:text-white">{{ $t($leader->full_name) }}</h3>
                            <p class="truncate text-xs text-brand-600 dark:text-brand-400">{{ $t($leader->position) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
