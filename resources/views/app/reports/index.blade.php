<x-layouts.app :title="__('Hisobotlar')">
    <div class="mx-auto max-w-7xl space-y-6">
        <x-app.page-header :title="__('Hisobotlar')" :subtitle="__('Zona faoliyati bo‘yicha umumiy ko‘rsatkichlar')" />

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
            <x-ui.stat :label="__('Jami arizalar')" :value="$totalApplications" icon="doc" color="brand" />
            <x-ui.stat :label="__('Ro‘yxatga olingan')" :value="$registered" icon="check-doc" color="success" />
            <x-ui.stat :label="__('Rad etilgan')" :value="$rejected" icon="alert" color="error" />
            <x-ui.stat :label="__('Faol rezidentlar')" :value="$activeResidents" icon="building" color="brand" />
            <x-ui.stat :label="__('To‘xtatilgan')" :value="$suspendedResidents" icon="building" color="warning" />
            <x-ui.stat :label="__('Berilgan guvohnomalar')" :value="$certificates" icon="shield" color="indigo" />
        </div>

        <div class="card p-6">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Arizalar holati bo‘yicha') }}</h2>
            <div class="mt-5 space-y-3">
                @php $maxVal = max(1, $byStatus->max() ?? 1); @endphp
                @foreach ($byStatus as $status => $count)
                    @php $enum = \App\Enums\ApplicationStatus::tryFrom($status); @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-300">{{ $enum?->label() ?? $status }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                            <div class="h-full rounded-full bg-brand-500" style="width: {{ round($count / $maxVal * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
