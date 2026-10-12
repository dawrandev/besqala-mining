{{-- News table body — rendered standalone for AJAX refresh. --}}
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400 dark:border-gray-800">
                <th class="px-6 py-3 font-medium">{{ __('Sarlavha') }}</th>
                <th class="hidden px-6 py-3 font-medium md:table-cell">{{ __('Sana') }}</th>
                <th class="px-6 py-3 font-medium">{{ __('Holati') }}</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($articles as $article)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $article->translate('title') }}</td>
                    <td class="hidden px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400">{{ $article->published_at?->translatedFormat('d M Y') ?? '—' }}</td>
                    <td class="px-6 py-4"><x-ui.badge :color="$article->isPublished() ? 'success' : 'gray'">{{ $article->isPublished() ? __('Chop etilgan') : __('Qoralama') }}</x-ui.badge></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" data-edit="{{ $article->id }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Tahrirlash') }}</button>
                            <button type="button" data-delete="{{ $article->id }}" class="text-sm font-medium text-error-600 hover:text-error-700">{{ __('O‘chirish') }}</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">{{ __('Yangiliklar yo‘q.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $articles->links() }}</div>
