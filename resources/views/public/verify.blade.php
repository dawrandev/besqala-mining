<x-layouts.public>
    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-50 text-accent-600 dark:bg-accent-500/15 dark:text-accent-400">
                <x-icon name="verify" class="h-7 w-7" />
            </span>
            <h1 class="mt-5 text-3xl font-bold text-gray-900 dark:text-white">{{ __('Reestr yozuvini tekshirish') }}</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Guvohnoma yoki ruxsatnoma raqamini kiriting.') }}</p>
        </div>

        <form method="GET" class="mx-auto mt-8 flex max-w-xl gap-3">
            <input type="text" name="no" value="{{ $number }}" placeholder="BMV-R-2026-000001"
                class="flex-1 rounded-xl border-gray-300 bg-white text-center font-mono text-sm shadow-theme-xs focus:border-accent-500 focus:ring-accent-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <button type="submit" class="rounded-xl bg-accent-500 px-6 py-3 text-sm font-semibold text-white hover:bg-accent-600">{{ __('Tekshirish') }}</button>
        </form>

        @if($searched)
            @if($document)
                @php
                    $banner = match($document->status) {
                        \App\Enums\DocumentStatus::Active => ['success', __('Amaldagi guvohnoma'), __('Ushbu guvohnoma hozirda amal qiladi.')],
                        \App\Enums\DocumentStatus::Suspended => ['warning', __('Amal vaqtincha to‘xtatilgan'), __('Guvohnoma amal qilishi vaqtincha to‘xtatib turilgan.')],
                        \App\Enums\DocumentStatus::Reissued => ['blue', __('Qayta rasmiylashtirilgan'), __('Ushbu guvohnoma yangisi bilan almashtirilgan.')],
                        \App\Enums\DocumentStatus::Terminated => ['gray', __('Amal tugatilgan'), __('Guvohnoma amal muddati tugagan yoki to‘xtatilgan.')],
                        \App\Enums\DocumentStatus::Annulled => ['error', __('Bekor qilingan'), __('Ushbu guvohnoma bekor qilingan.')],
                        default => ['gray', $document->status->label(), ''],
                    };
                    $bannerBg = [
                        'success' => 'bg-success-50 dark:bg-success-500/10',
                        'warning' => 'bg-warning-50 dark:bg-warning-500/10',
                        'blue' => 'bg-blue-light-50 dark:bg-blue-light-500/10',
                        'gray' => 'bg-gray-50 dark:bg-gray-800/40',
                        'error' => 'bg-error-50 dark:bg-error-500/10',
                    ][$banner[0]];
                    $publicEvents = $document->statusEvents->filter(fn ($e) => $e->isPublic());
                @endphp

                <div class="mx-auto mt-10 max-w-2xl space-y-6">
                    {{-- Status banner --}}
                    <div class="card overflow-hidden">
                        <div class="flex items-start gap-3 {{ $bannerBg }} px-6 py-5">
                            <x-ui.badge :color="$document->status->color()">{{ $document->status->label() }}</x-ui.badge>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $banner[1] }}</p>
                                @if($banner[2])<p class="mt-0.5 text-sm text-gray-600 dark:text-gray-300">{{ $banner[2] }}</p>@endif
                                @if($document->status === \App\Enums\DocumentStatus::Reissued && $document->replacedBy)
                                    <a href="{{ route('verify', ['no' => $document->replacedBy->number]) }}" class="mt-1 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">
                                        {{ __('Yangi guvohnoma') }}: {{ $document->replacedBy->number }} <x-icon name="arrow-right" class="h-4 w-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Registry record (Regulation point 21) --}}
                    <div class="card overflow-hidden">
                        <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                            <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Reestr yozuvi') }}</h2>
                            <p class="text-xs text-gray-400">{{ __('Maxsus mayning zonasi rezidenti to‘g‘risidagi ochiq ma‘lumotlar') }}</p>
                        </div>
                        <dl class="divide-y divide-gray-100 px-6 dark:divide-gray-800">
                            @php
                                $rows = [
                                    [__('To‘liq nomi va tashkiliy-huquqiy shakli'), $record['company_name']],
                                    [__('Joylashgan yeri (pochta manzili)'), $record['location_address'] ?: $record['address']],
                                    [__('Elektron pochta manzili'), $record['email']],
                                    [__('STIR'), $record['stir']],
                                    [__('Telefon raqami'), $record['phone']],
                                    [__('Guvohnoma tartib raqami'), $document->number],
                                    [__('Berilgan sana'), $document->issued_at?->translatedFormat('d F Y')],
                                    [__('Amal qilish muddati'), $document->expires_at?->translatedFormat('d F Y') ?? '—'],
                                ];
                            @endphp
                            @foreach ($rows as [$k, $v])
                                <div class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:justify-between sm:gap-6">
                                    <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ $k }}</dt>
                                    <dd class="font-medium text-gray-900 sm:text-right dark:text-white">{{ $v ?: '—' }}</dd>
                                </div>
                            @endforeach

                            {{-- Founders and shares --}}
                            <div class="py-3 text-sm">
                                <dt class="mb-1.5 text-gray-500 dark:text-gray-400">{{ __('Ta‘sischilar va ustav fondidagi ulushlari') }}</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    @forelse (collect($record['founders'])->filter(fn ($f) => ($f['name'] ?? '') !== '') as $f)
                                        <div class="flex justify-between gap-6 border-b border-gray-50 py-1 last:border-0 dark:border-gray-800/60">
                                            <span>{{ $f['name'] }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">{{ isset($f['share']) ? $f['share'].'%' : '—' }}</span>
                                        </div>
                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </dd>
                            </div>
                        </dl>
                        <div class="border-t border-gray-100 px-6 py-4 dark:border-gray-800">
                            <a href="{{ route('verify.pdf', ['number' => $document->number]) }}" class="inline-flex items-center gap-2 rounded-xl bg-accent-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-accent-600">
                                <x-icon name="doc" class="h-4 w-4" /> {{ __('Guvohnomani yuklab olish (PDF)') }}
                            </a>
                        </div>
                    </div>

                    {{-- Status history (grounds and dates) --}}
                    @if($publicEvents->isNotEmpty())
                        <div class="card overflow-hidden">
                            <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                                <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Holat o‘zgarishlari tarixi') }}</h2>
                            </div>
                            <div class="p-6">
                                <ol class="relative space-y-5 border-l border-gray-200 pl-5 dark:border-gray-700">
                                    @foreach ($publicEvents as $event)
                                        <li class="relative">
                                            <span class="absolute -left-[1.6rem] top-1 flex h-3 w-3 rounded-full border-2 border-white bg-accent-500 dark:border-gray-900"></span>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $event->publicLabel() }}</p>
                                            @if($event->reason)<p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Asos') }}: {{ $event->reason }}</p>@endif
                                            <p class="text-xs text-gray-400">{{ $event->created_at?->translatedFormat('d F Y, H:i') }}</p>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="mx-auto mt-8 max-w-xl">
                    <div class="card flex items-center gap-3 border-error-200 p-6 dark:border-error-500/30">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-error-50 text-error-600 dark:bg-error-500/15"><x-icon name="alert" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ __('Hujjat topilmadi') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('«:no» raqamli rasmiy hujjat reestrda mavjud emas.', ['no' => $number]) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </section>
</x-layouts.public>
