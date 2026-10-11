<x-layouts.app :title="__('Mening arizalarim')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Mening arizalarim')" :subtitle="__('Barcha topshirgan arizalaringiz')">
            <x-slot:actions>
                <a href="{{ route('app.applications.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600"><x-icon name="plus" class="h-4 w-4" /> {{ __('Yangi ariza') }}</a>
            </x-slot:actions>
        </x-app.page-header>
        <x-app.application-table :applications="$applications" :empty="__('Hali arizalar yo‘q.')" />
    </div>
</x-layouts.app>
