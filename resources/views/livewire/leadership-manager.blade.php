<div>
    @if (session('status'))
        <div class="mb-4 rounded-xl bg-success-50 px-4 py-3 text-sm font-medium text-success-700 dark:bg-success-500/10">{{ session('status') }}</div>
    @endif

    <div class="mb-4 flex justify-end">
        <button wire:click="create" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600"><x-icon name="plus" class="h-4 w-4" /> {{ __('A‘zo qo‘shish') }}</button>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($leaders as $leader)
            <div wire:key="leader-{{ $leader->id }}" class="card p-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">
                        {{ \Illuminate\Support\Str::of($leader->translate('full_name'))->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                    </span>
                    <div class="min-w-0">
                        <h3 class="truncate font-semibold text-gray-900 dark:text-white">{{ $leader->translate('full_name') }}</h3>
                        <p class="truncate text-xs text-brand-600 dark:text-brand-400">{{ $leader->translate('position') }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3 border-t border-gray-100 pt-3 dark:border-gray-800">
                    <button wire:click="edit({{ $leader->id }})" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Tahrirlash') }}</button>
                    <button wire:click="delete({{ $leader->id }})" wire:confirm="{{ __('O‘chirishni tasdiqlaysizmi?') }}" class="text-sm font-medium text-error-600 hover:text-error-700">{{ __('O‘chirish') }}</button>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-400">{{ __('A‘zolar yo‘q.') }}</p>
        @endforelse
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ loc: 'uz' }">
            <div class="absolute inset-0 bg-gray-900/50" wire:click="$set('showForm', false)"></div>
            <div class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-theme-xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $editingId ? __('A‘zoni tahrirlash') : __('Yangi a‘zo') }}</h3>

                <div class="mt-4 inline-flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
                    @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $lbl)
                        <button type="button" @click="loc = '{{ $code }}'" :class="loc === '{{ $code }}' ? 'bg-white text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-semibold">{{ $lbl }}</button>
                    @endforeach
                </div>

                <div class="mt-4 space-y-4">
                    @foreach (['uz', 'ru', 'en'] as $code)
                        <div x-show="loc === '{{ $code }}'" class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('F.I.Sh.') }} ({{ strtoupper($code) }})</label>
                                <input type="text" wire:model="full_name.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @if($code === 'uz') @error('full_name.uz')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror @endif
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lavozim') }} ({{ strtoupper($code) }})</label>
                                <input type="text" wire:model="position.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @if($code === 'uz') @error('position.uz')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror @endif
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bo‘lim') }} ({{ strtoupper($code) }})</label>
                                <input type="text" wire:model="department.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Biografiya') }} ({{ strtoupper($code) }})</label>
                                <textarea wire:model="bio.{{ $code }}" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                        </div>
                    @endforeach
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tartib raqami') }}</label>
                        <input type="number" wire:model="sort_order" class="w-32 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('showForm', false)" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">{{ __('Bekor qilish') }}</button>
                    <button wire:click="save" class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Saqlash') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
