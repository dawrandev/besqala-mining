<x-layouts.app :title="__('Nazorat')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Nazorat va dalolatnomalar')" :subtitle="__('Rezidentlar faoliyati ustidan nazorat')" />
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400 dark:border-gray-800">
                            <th class="px-6 py-3 font-medium">{{ __('Dalolatnoma') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Rezident') }}</th>
                            <th class="hidden px-6 py-3 font-medium md:table-cell">{{ __('Mavzu') }}</th>
                            <th class="hidden px-6 py-3 font-medium lg:table-cell">{{ __('Sana') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Natija') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($inspections as $inspection)
                            <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $inspection->act_number }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $inspection->resident?->company_name }}</td>
                                <td class="hidden px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400">{{ $inspection->subject }}</td>
                                <td class="hidden px-6 py-4 text-gray-500 lg:table-cell dark:text-gray-400">{{ $inspection->conducted_at?->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4"><x-ui.badge :color="$inspection->has_violation ? 'error' : 'success'">{{ $inspection->has_violation ? __('Buzilish') : __('Toza') }}</x-ui.badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">{{ __('Dalolatnomalar yo‘q.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($inspections->hasPages())<div class="mt-5">{{ $inspections->links() }}</div>@endif
    </div>
</x-layouts.app>
