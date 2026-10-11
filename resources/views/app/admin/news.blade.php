<x-layouts.app :title="__('Yangiliklar')">
    @php $t = fn ($arr) => is_array($arr) ? ($arr[app()->getLocale()] ?? $arr['uz'] ?? '') : $arr; @endphp
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Yangiliklar')" :subtitle="__('Yangiliklarni qo‘shish va tahrirlash')" />
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400 dark:border-gray-800">
                            <th class="px-6 py-3 font-medium">{{ __('Sarlavha') }}</th>
                            <th class="hidden px-6 py-3 font-medium md:table-cell">{{ __('Sana') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('Holati') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($articles as $article)
                            <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $t($article->title) }}</td>
                                <td class="hidden px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400">{{ $article->published_at?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="px-6 py-4"><x-ui.badge :color="$article->isPublished() ? 'success' : 'gray'">{{ $article->isPublished() ? __('Chop etilgan') : __('Qoralama') }}</x-ui.badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-gray-400">{{ __('Yangiliklar yo‘q.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($articles->hasPages())<div class="mt-5">{{ $articles->links() }}</div>@endif
    </div>
</x-layouts.app>
