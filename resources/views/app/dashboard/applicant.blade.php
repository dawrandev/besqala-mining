<x-layouts.app :title="__('Boshqaruv paneli')">
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Xush kelibsiz') }}</h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">{{ auth()->user()->name }}</p>
            </div>
            <a href="{{ route('app.applications.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-theme-sm hover:bg-brand-600">
                <x-icon name="plus" class="h-4 w-4" /> {{ __('Yangi ariza') }}
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-ui.stat :label="__('Faol rezidentlik')" :value="$activeResidents" :caption="__('ro‘yxatdan o‘tgan')" icon="check-doc" color="success" />
            <x-ui.stat :label="__('Jarayonda')" :value="$inProgress" :caption="__('ko‘rib chiqilmoqda')" icon="doc" color="indigo" />
            <x-ui.stat :label="__('Harakat talab qilinadi')" :value="$deficiencies" :caption="__('kamchilik')" icon="alert" :color="$deficiencies > 0 ? 'warning' : 'brand'" />
            <x-ui.stat :label="__('Jami qazib olindi')" :value="number_format($minedTotal, 2).' BTC'" :caption="__('barcha davr')" icon="bitcoin" color="brand" />
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white"><x-icon name="doc" class="h-5 w-5 text-brand-500" /> {{ __('Mening arizalarim') }}</h2>
                    <a href="{{ route('app.applications') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Barchasi') }}</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($applications as $application)
                        <a href="{{ route('app.applications.show', $application) }}" class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-white/5">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</p>
                                <p class="text-xs text-gray-400">{{ $application->number }} · {{ $application->region->label() }}</p>
                            </div>
                            <x-ui.badge :color="$application->status->color()">{{ $application->status->label() }}</x-ui.badge>
                        </a>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-gray-400">{{ __('Hali arizalar yo‘q.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white"><x-icon name="user" class="h-5 w-5 text-brand-500" /> {{ __('Shaxsiy kabinet') }}</h2>
                    @if($certificate)
                        <a href="{{ route('app.cabinet') }}" class="mt-4 flex items-center justify-between rounded-xl bg-brand-50 p-4 transition hover:bg-brand-100 dark:bg-brand-500/10">
                            <div>
                                <p class="font-mono text-sm font-semibold text-brand-700 dark:text-brand-400">{{ $certificate->number }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Amal qiladi') }}: {{ $certificate->expires_at?->translatedFormat('d M Y') }}</p>
                            </div>
                            <x-icon name="arrow-right" class="h-5 w-5 text-brand-500" />
                        </a>
                    @else
                        <p class="mt-4 text-sm text-gray-400">{{ __('Guvohnoma hali berilmagan.') }}</p>
                    @endif
                </div>

                <div class="card p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Tezkor havolalar') }}</h2>
                    <div class="mt-4 space-y-1">
                        @foreach ([
                            ['app.applications.create', __('Yangi ariza topshirish'), 'plus'],
                            ['registry', __('Ochiq reestr'), 'registry'],
                            ['verify', __('Hujjatni tekshirish'), 'verify'],
                        ] as [$route, $label, $icon])
                            <a href="{{ route($route) }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm text-gray-600 transition hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                <span class="flex items-center gap-2.5"><x-icon :name="$icon" class="h-4 w-4 text-gray-400" /> {{ $label }}</span>
                                <x-icon name="arrow-right" class="h-4 w-4 text-gray-300" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
