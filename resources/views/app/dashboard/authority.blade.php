<x-layouts.app :title="__('Vakolatli organ')">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Vakolatli organ') }}</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ __('Ruxsatnomalar berish va nazoratni amalga oshirish') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-ui.stat :label="__('Ruxsatnoma kutilmoqda')" :value="$pendingPermits" :caption="__('8 kun muddat')" icon="shield" color="indigo" />
            <x-ui.stat :label="__('Faol ruxsatnomalar')" :value="$activePermits" :caption="__('amal qilmoqda')" icon="verify" color="success" />
            <x-ui.stat :label="__('To‘xtatib turilgan')" :value="$suspended" :caption="__('rezidentlar')" icon="building" color="warning" />
            <x-ui.stat :label="__('Aniqlangan buzilishlar')" :value="$violations" :caption="__('dalolatnomalar')" icon="alert" :color="$violations > 0 ? 'error' : 'brand'" />
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
            <div class="card lg:col-span-3">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Ruxsatnoma navbati') }}</h2>
                    <a href="{{ route('app.permits') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Barchasi') }}</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($queue as $application)
                        <a href="{{ route('app.applications.show', $application) }}" class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-white/5">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</p>
                                <p class="text-xs text-gray-400">{{ $application->number }} · {{ $application->region->label() }}</p>
                            </div>
                            <x-ui.badge color="indigo">{{ __('Vakolatli organda') }}</x-ui.badge>
                        </a>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-gray-400">{{ __('Navbatda ariza yo‘q.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card lg:col-span-2">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('So‘nggi dalolatnomalar') }}</h2>
                    <a href="{{ route('app.oversight') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Nazorat') }}</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($inspections as $inspection)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $inspection->resident?->company_name }}</p>
                                <x-ui.badge :color="$inspection->has_violation ? 'error' : 'success'">{{ $inspection->has_violation ? __('Buzilish') : __('Toza') }}</x-ui.badge>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">{{ $inspection->subject }}</p>
                        </div>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-gray-400">{{ __('Dalolatnomalar yo‘q.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
