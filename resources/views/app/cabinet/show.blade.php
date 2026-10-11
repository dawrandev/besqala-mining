<x-layouts.app :title="$resident->company_name">
    <div class="mx-auto max-w-5xl space-y-6">
        <a href="{{ route('app.cabinet') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-brand-600"><x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> {{ __('Shaxsiy kabinet') }}</a>

        <div class="card p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $resident->company_name }}</h1>
                    <p class="mt-1 text-sm text-gray-400">{{ $resident->legal_form }}</p>
                </div>
                <x-ui.badge :color="$resident->status->color()">{{ $resident->status->label() }}</x-ui.badge>
            </div>
            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ([
                    [__('STIR'), $resident->stir],
                    [__('Hudud'), $resident->region->label()],
                    [__('Manzil'), $resident->address],
                    [__('Joylashuv'), $resident->location_address ?? '—'],
                    [__('Email'), $resident->email ?? '—'],
                    [__('Telefon'), $resident->phone ?? '—'],
                ] as [$k, $v])
                    <div><dt class="text-xs text-gray-400">{{ $k }}</dt><dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $v }}</dd></div>
                @endforeach
            </dl>
        </div>

        <div class="card">
            <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Hujjatlar') }}</h2></div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($resident->documents as $doc)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-mono text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $doc->number }}</p>
                            <p class="text-xs text-gray-400">{{ $doc->kind->label() }} · {{ $doc->issued_at?->translatedFormat('d M Y') }}</p>
                        </div>
                        <a href="{{ route('verify', ['no' => $doc->number]) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">{{ __('Tekshirish') }}</a>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-400">{{ __('Hujjatlar yo‘q.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
