{{-- Leadership members grid — rendered standalone for AJAX refresh. --}}
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($leaders as $leader)
        <div class="card p-6">
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
                <button type="button" data-edit="{{ $leader->id }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Tahrirlash') }}</button>
                <button type="button" data-delete="{{ $leader->id }}" class="text-sm font-medium text-error-600 hover:text-error-700">{{ __('O‘chirish') }}</button>
            </div>
        </div>
    @empty
        <p class="col-span-full text-center text-gray-400">{{ __('A‘zolar yo‘q.') }}</p>
    @endforelse
</div>
