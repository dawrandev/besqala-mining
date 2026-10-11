<x-layouts.public>
    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                <x-icon name="verify" class="h-7 w-7" />
            </span>
            <h1 class="mt-5 text-3xl font-bold text-gray-900 dark:text-white">{{ __('Hujjatni tekshirish') }}</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Guvohnoma yoki ruxsatnoma raqamini kiriting.') }}</p>
        </div>

        <form method="GET" class="mx-auto mt-8 flex max-w-xl gap-3">
            <input type="text" name="no" value="{{ $number }}" placeholder="G-2026-0001"
                class="flex-1 rounded-xl border-gray-300 bg-white text-center font-mono text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <button type="submit" class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Tekshirish') }}</button>
        </form>

        @if($searched)
            <div class="mx-auto mt-8 max-w-xl">
                @if($document)
                    <div class="card overflow-hidden">
                        <div class="flex items-center gap-3 border-b border-gray-100 bg-success-50 px-6 py-4 dark:border-gray-800 dark:bg-success-500/10">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-success-500 text-white"><x-icon name="verify" class="h-5 w-5" /></span>
                            <div>
                                <p class="font-semibold text-success-700 dark:text-success-500">{{ __('Hujjat haqiqiy') }}</p>
                                <p class="text-sm text-success-600/80">{{ $document->kind->label() }} · {{ $document->status->label() }}</p>
                            </div>
                        </div>
                        <dl class="divide-y divide-gray-100 px-6 dark:divide-gray-800">
                            @foreach ([
                                [__('Raqam'), $document->number],
                                [__('Turi'), $document->kind->label()],
                                [__('Rezident'), $document->resident?->company_name ?? '—'],
                                [__('Berilgan sana'), $document->issued_at?->translatedFormat('d F Y')],
                                [__('Amal qilish muddati'), $document->expires_at?->translatedFormat('d F Y') ?? '—'],
                                [__('Holati'), $document->status->label()],
                            ] as [$k, $v])
                                <div class="flex justify-between py-3 text-sm">
                                    <dt class="text-gray-500 dark:text-gray-400">{{ $k }}</dt>
                                    <dd class="font-medium text-gray-900 dark:text-white">{{ $v }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @else
                    <div class="card flex items-center gap-3 border-error-200 p-6 dark:border-error-500/30">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-error-50 text-error-600 dark:bg-error-500/15"><x-icon name="alert" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ __('Hujjat topilmadi') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('«:no» raqamli hujjat reestrda mavjud emas.', ['no' => $number]) }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </section>
</x-layouts.public>
