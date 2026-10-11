@props(['color' => 'gray'])

@php
$map = [
    'gray' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    'brand' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400',
    'success' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
    'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500',
    'error' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500',
    'blue' => 'bg-blue-light-50 text-blue-light-600 dark:bg-blue-light-500/15 dark:text-blue-light-500',
    'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-500',
];
$classes = $map[$color] ?? $map['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
