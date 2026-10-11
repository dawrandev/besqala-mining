<div x-data="{ loc: 'uz' }">
    @if (session('status'))
        <div class="mb-4 rounded-xl bg-success-50 px-4 py-3 text-sm font-medium text-success-700 dark:bg-success-500/10">{{ session('status') }}</div>
    @endif

    <div class="mb-4 inline-flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
        @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $lbl)
            <button type="button" @click="loc = '{{ $code }}'" :class="loc === '{{ $code }}' ? 'bg-white text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-semibold">{{ $lbl }}</button>
        @endforeach
    </div>

    <div class="card space-y-4 p-6">
        @foreach (['uz', 'ru', 'en'] as $code)
            <div x-show="loc === '{{ $code }}'" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sarlavha') }} ({{ strtoupper($code) }})</label>
                    <input type="text" wire:model="title.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    @if($code === 'uz') @error('title.uz')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror @endif
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kirish matni') }} ({{ strtoupper($code) }})</label>
                    <textarea wire:model="intro.{{ $code }}" rows="5" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                    @if($code === 'uz') @error('intro.uz')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-6 p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Statistika') }}</h3>
            <button type="button" wire:click="addStat" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 dark:text-brand-400"><x-icon name="plus" class="h-4 w-4" /> {{ __('Qo‘shish') }}</button>
        </div>
        <div class="space-y-3">
            @foreach ($stats as $index => $stat)
                <div class="flex items-center gap-3" wire:key="stat-{{ $index }}">
                    <input type="text" wire:model="stats.{{ $index }}.label.uz" placeholder="{{ __('Nomi (uz)') }}" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <input type="text" wire:model="stats.{{ $index }}.value" placeholder="{{ __('Qiymat') }}" class="w-32 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <button type="button" wire:click="removeStat({{ $index }})" class="rounded-lg p-2 text-gray-400 hover:bg-error-50 hover:text-error-500 dark:hover:bg-error-500/10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button wire:click="save" class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Saqlash') }}</button>
    </div>
</div>
