<x-layouts.app :title="__('Direksiya boshqaruv paneli')">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Direksiya boshqaruv paneli') }}</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ __('Arizalarni ko‘rib chiqish va rezidentlarni ro‘yxatga olish') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-ui.stat :label="__('Yangi arizalar')" :value="$newApplications" :caption="__('to‘liqlik tekshiruvi')" icon="doc" color="indigo" />
            <x-ui.stat :label="__('Ekspertda')" :value="$atExpert" :caption="__('xulosa kutilmoqda')" icon="check-doc" color="brand" />
            <x-ui.stat :label="__('Ro‘yxatga olish')" :value="$issued" :caption="__('ruxsatnoma berilgan')" icon="shield" color="success" />
            <x-ui.stat :label="__('Faol rezidentlar')" :value="$activeResidents" :caption="$suspendedResidents.' '.__('to‘xtatilgan')" icon="building" color="brand" />
        </div>

        @php
            $areaOptions = [
                'chart' => ['type' => 'area', 'height' => 210, 'fontFamily' => 'Outfit, sans-serif', 'toolbar' => ['show' => false]],
                'series' => [['name' => __('Arizalar'), 'data' => $monthly['data']]],
                'xaxis' => ['categories' => $monthly['labels'], 'axisBorder' => ['show' => false], 'axisTicks' => ['show' => false]],
                'yaxis' => ['labels' => ['style' => ['colors' => '#98a2b3']]],
                'colors' => ['#465fff'],
                'stroke' => ['curve' => 'smooth', 'width' => 2],
                'fill' => ['type' => 'gradient', 'gradient' => ['opacityFrom' => 0.4, 'opacityTo' => 0.02]],
                'dataLabels' => ['enabled' => false],
                'grid' => ['strokeDashArray' => 4, 'borderColor' => '#e4e7ec'],
                'tooltip' => ['theme' => 'light'],
            ];
            $donutOptions = [
                'chart' => ['type' => 'donut', 'height' => 240, 'fontFamily' => 'Outfit, sans-serif'],
                'series' => $statusBreakdown['data'],
                'labels' => $statusBreakdown['labels'],
                'colors' => $statusBreakdown['colors'],
                'legend' => ['position' => 'bottom', 'fontSize' => '13px'],
                'dataLabels' => ['enabled' => false],
                'plotOptions' => ['pie' => ['donut' => ['size' => '68%']]],
                'stroke' => ['width' => 0],
            ];
        @endphp

        <div class="grid gap-6 lg:grid-cols-5">
            <div class="card p-6 lg:col-span-3">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Jami arizalar') }}</h2>
                    <span class="text-xs text-gray-400">{{ __('barcha davr') }}</span>
                </div>
                <x-admin.chart :options="$areaOptions" />
            </div>
            <div class="card p-6 lg:col-span-2">
                <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">{{ __('Arizalar holati bo‘yicha') }}</h2>
                @if (count($statusBreakdown['data']) > 0)
                    <x-admin.chart :options="$donutOptions" />
                @else
                    <p class="py-12 text-center text-sm text-gray-400">{{ __('Ma‘lumot yo‘q.') }}</p>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-5">
            <div class="card lg:col-span-3">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('To‘liqlik tekshiruvi navbati') }}</h2>
                    <a href="{{ route('app.queue') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Navbat') }}</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($queue as $application)
                        <a href="{{ route('app.applications.show', $application) }}" class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-white/5">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</p>
                                <p class="text-xs text-gray-400">{{ $application->number }} · {{ $application->region->label() }}</p>
                            </div>
                            <x-ui.badge :color="$application->status->color()">{{ $application->status->label() }}</x-ui.badge>
                        </a>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-gray-400">{{ __('Navbatda ariza yo‘q.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card lg:col-span-2">
                <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('So‘nggi harakatlar') }}</h2>
                </div>
                <div class="space-y-4 p-6">
                    @forelse ($events as $event)
                        <div class="flex gap-3">
                            <span class="mt-1 flex h-2 w-2 shrink-0 rounded-full bg-brand-400"></span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $event->title }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $event->application?->company_name }} · {{ $event->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400">{{ __('Harakatlar yo‘q.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
