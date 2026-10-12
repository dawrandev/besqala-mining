@props(['dark' => false, 'compact' => false])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-accent-500 to-accent-700 shadow-theme-sm">
        <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 20 6-10 4 6 2-3 6 7z" fill="currentColor" stroke="none" />
            <circle cx="16" cy="6" r="2.2" fill="currentColor" stroke="none" opacity="0.85" />
        </svg>
    </span>
    @unless($compact)
        <span class="leading-tight">
            <span class="block text-base font-bold {{ $dark ? 'text-white' : 'text-gray-900 dark:text-white' }}">Besqala</span>
            <span class="block text-xs font-medium text-accent-500">Mining Valley</span>
        </span>
    @endunless
</a>
