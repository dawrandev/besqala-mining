@props(['label', 'value', 'caption' => null, 'icon' => null, 'color' => 'brand'])

@php
$iconBg = [
    'brand' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
    'success' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
    'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-500',
    'error' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
    'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-500',
][$color] ?? 'bg-brand-50 text-brand-600';
@endphp

<div class="card p-5">
    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</span>
        @if($icon)
            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $iconBg }}">
                {!! $icon !!}
            </span>
        @endif
    </div>
    <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
    @if($caption)
        <p class="mt-1 text-xs text-gray-400">{{ $caption }}</p>
    @endif
</div>
