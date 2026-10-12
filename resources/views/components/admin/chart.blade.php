@props(['options'])

{{-- Renders an ApexCharts instance. `options` is a full ApexCharts config array. --}}
<div wire:ignore
    x-data
    x-init="$nextTick(() => { if (window.ApexCharts) { new window.ApexCharts($el, {{ \Illuminate\Support\Js::from($options) }}).render(); } })"
    {{ $attributes }}>
</div>
