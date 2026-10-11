<x-layouts.app :title="__('Biz haqimizda')">
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : ($arr ?? ''); @endphp
    <div class="mx-auto max-w-4xl space-y-6">
        <x-app.page-header :title="__('Biz haqimizda')" :subtitle="__('Sahifa matni va statistikasi')" />
        @if($about)
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $t($about['title']) }}</h2>
                <p class="mt-3 leading-relaxed text-gray-600 dark:text-gray-300">{{ $t($about['intro']) }}</p>
            </div>
            @if(!empty($about['stats']))
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($about['stats'] as $stat)
                        <div class="card p-6 text-center">
                            <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $t($stat['label']) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="card p-12 text-center"><p class="text-gray-400">{{ __('Sahifa to‘ldirilmagan.') }}</p></div>
        @endif
    </div>
</x-layouts.app>
