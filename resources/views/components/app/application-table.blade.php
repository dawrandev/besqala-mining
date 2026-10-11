@props(['applications', 'empty' => null])

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400 dark:border-gray-800">
                    <th class="px-6 py-3 font-medium">{{ __('Ariza') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('Korxona') }}</th>
                    <th class="hidden px-6 py-3 font-medium md:table-cell">{{ __('Hudud') }}</th>
                    <th class="hidden px-6 py-3 font-medium lg:table-cell">{{ __('Topshirilgan') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('Holati') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($applications as $application)
                    <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $application->number }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</td>
                        <td class="hidden px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400">{{ $application->region->label() }}</td>
                        <td class="hidden px-6 py-4 text-gray-500 lg:table-cell dark:text-gray-400">{{ $application->submitted_at?->translatedFormat('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-ui.badge :color="$application->status->color()">{{ $application->status->label() }}</x-ui.badge></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('app.applications.show', $application) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Ochish') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ $empty ?? __('Ma‘lumot yo‘q.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($applications instanceof \Illuminate\Contracts\Pagination\Paginator && $applications->hasPages())
    <div class="mt-5">{{ $applications->links() }}</div>
@endif
