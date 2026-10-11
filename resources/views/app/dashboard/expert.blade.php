<x-layouts.app :title="__('Ekspert komissiyasi')">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Ekspert komissiyasi') }}</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ __('Arizalarni ko‘rib chiqish va xulosa tayyorlash') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-ui.stat :label="__('Ko‘rib chiqish kutilmoqda')" :value="$pending" :caption="__('15 kun muddat')" icon="check-doc" color="indigo" />
            <x-ui.stat :label="__('Ijobiy xulosalar')" :value="$positive" :caption="__('jami')" icon="verify" color="success" />
            <x-ui.stat :label="__('Salbiy xulosalar')" :value="$negative" :caption="__('jami')" icon="alert" color="error" />
        </div>

        <div class="card">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Ko‘rib chiqish navbati') }}</h2>
                <a href="{{ route('app.review') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Barchasi') }}</a>
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
                    <p class="px-6 py-12 text-center text-sm text-gray-400">{{ __('Ko‘rib chiqish uchun ariza yo‘q') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
