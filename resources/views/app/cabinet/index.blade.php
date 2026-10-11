<x-layouts.app :title="__('Shaxsiy kabinet')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Shaxsiy kabinet')" :subtitle="__('Rezident korxonalaringiz va hujjatlaringiz')" />
        <div class="grid gap-6 md:grid-cols-2">
            @forelse ($residents as $resident)
                <div class="card p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400"><x-icon name="building" /></span>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $resident->company_name }}</h3>
                                <p class="text-xs text-gray-400">{{ __('STIR') }}: {{ $resident->stir }}</p>
                            </div>
                        </div>
                        <x-ui.badge :color="$resident->status->color()">{{ $resident->status->label() }}</x-ui.badge>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-400">{{ __('Hudud') }}</dt><dd class="text-gray-700 dark:text-gray-300">{{ $resident->region->label() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-400">{{ __('Quvvat') }}</dt><dd class="text-gray-700 dark:text-gray-300">{{ $resident->power_mw ? number_format((float)$resident->power_mw, 1).' MW' : '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-400">{{ __('Qazib olindi') }}</dt><dd class="text-gray-700 dark:text-gray-300">{{ number_format((float)$resident->mined_total_btc, 2) }} BTC</dd></div>
                    </dl>
                    <a href="{{ route('app.cabinet.show', $resident) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Batafsil') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
            @empty
                <div class="card col-span-full p-12 text-center">
                    <p class="text-gray-400">{{ __('Sizda hali rezident korxona yo‘q.') }}</p>
                    <a href="{{ route('app.applications.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600"><x-icon name="plus" class="h-4 w-4" /> {{ __('Ariza topshirish') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>
