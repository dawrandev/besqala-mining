@props(['dark' => false])

@php $locales = ['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN']; @endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5 rounded-lg border p-0.5 '.($dark ? 'border-white/15' : 'border-gray-200 dark:border-gray-800')]) }}>
    @foreach($locales as $code => $label)
        <form method="POST" action="{{ route('locale.update', $code) }}">
            @csrf
            <button type="submit"
                class="rounded-md px-2 py-1 text-xs font-semibold transition-colors
                    {{ app()->getLocale() === $code
                        ? 'bg-brand-500 text-white'
                        : ($dark ? 'text-white/70 hover:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white') }}">
                {{ $label }}
            </button>
        </form>
    @endforeach
</div>
