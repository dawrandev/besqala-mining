<x-layouts.app :title="__('Rezidentlar')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Rezidentlar')" :subtitle="__('Ro‘yxatga olingan rezident korxonalar')" />
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400 dark:border-gray-800">
                            <th class="px-6 py-3 font-medium">{{ __('Korxona') }}</th>
                            <th class="hidden px-6 py-3 font-medium md:table-cell">{{ __('STIR') }}</th>
                            <th class="hidden px-6 py-3 font-medium md:table-cell">{{ __('Hudud') }}</th>
                            <th class="hidden px-6 py-3 font-medium lg:table-cell">{{ __('Arizalar') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Holati') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($residents as $resident)
                            <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $resident->company_name }}</td>
                                <td class="hidden px-6 py-4 font-mono text-xs text-gray-500 md:table-cell dark:text-gray-400">{{ $resident->stir }}</td>
                                <td class="hidden px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400">{{ $resident->region->label() }}</td>
                                <td class="hidden px-6 py-4 text-gray-500 lg:table-cell dark:text-gray-400">{{ $resident->applications_count }}</td>
                                <td class="px-6 py-4"><x-ui.badge :color="$resident->status->color()">{{ $resident->status->label() }}</x-ui.badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">{{ __('Rezidentlar yo‘q.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($residents->hasPages())<div class="mt-5">{{ $residents->links() }}</div>@endif
    </div>
</x-layouts.app>
