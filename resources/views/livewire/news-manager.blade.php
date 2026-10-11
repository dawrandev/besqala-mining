<div>
    @if (session('status'))
        <div class="mb-4 rounded-xl bg-success-50 px-4 py-3 text-sm font-medium text-success-700 dark:bg-success-500/10">{{ session('status') }}</div>
    @endif

    <div class="mb-4 flex justify-end">
        <button wire:click="create" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600"><x-icon name="plus" class="h-4 w-4" /> {{ __('Yangilik qo‘shish') }}</button>
    </div>

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
                    <tr wire:key="news-{{ $article->id }}" class="hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $article->translate('title') }}</td>
                        <td class="hidden px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400">{{ $article->published_at?->translatedFormat('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-ui.badge :color="$article->isPublished() ? 'success' : 'gray'">{{ $article->isPublished() ? __('Chop etilgan') : __('Qoralama') }}</x-ui.badge></td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="edit({{ $article->id }})" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Tahrirlash') }}</button>
                                <button wire:click="delete({{ $article->id }})" wire:confirm="{{ __('O‘chirishni tasdiqlaysizmi?') }}" class="text-sm font-medium text-error-600 hover:text-error-700">{{ __('O‘chirish') }}</button>
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

    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ loc: 'uz' }">
            <div class="absolute inset-0 bg-gray-900/50" wire:click="$set('showForm', false)"></div>
            <div class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-theme-xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $editingId ? __('Yangilikni tahrirlash') : __('Yangi yangilik') }}</h3>

                <div class="mt-4 inline-flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
                    @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $lbl)
                        <button type="button" @click="loc = '{{ $code }}'" :class="loc === '{{ $code }}' ? 'bg-white text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-semibold">{{ $lbl }}</button>
                    @endforeach
                </div>

                <div class="mt-4 space-y-4">
                    @foreach (['uz', 'ru', 'en'] as $code)
                        <div x-show="loc === '{{ $code }}'" class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sarlavha') }} ({{ strtoupper($code) }})</label>
                                <input type="text" wire:model="title.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @if($code === 'uz') @error('title.uz')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror @endif
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Qisqa matn') }} ({{ strtoupper($code) }})</label>
                                <textarea wire:model="excerpt.{{ $code }}" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Matn') }} ({{ strtoupper($code) }})</label>
                                <textarea wire:model="body.{{ $code }}" rows="5" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                                @if($code === 'uz') @error('body.uz')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror @endif
                            </div>
                        </div>
                    @endforeach

                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" wire:model="published" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500"> {{ __('Chop etish') }}
                    </label>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('showForm', false)" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">{{ __('Bekor qilish') }}</button>
                    <button wire:click="save" class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Saqlash') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
