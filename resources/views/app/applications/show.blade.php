<x-layouts.app :title="$application->number">
    <div class="mx-auto max-w-5xl space-y-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-brand-600"><x-icon name="arrow-right" class="h-4 w-4 rotate-180" /> {{ __('Orqaga') }}</a>

        {{-- Workflow progress stepper --}}
        @php
            $steps = [__('Topshirildi'), __('To‘liqlik tekshiruvi'), __('Ekspert xulosasi'), __('Ruxsatnoma'), __('Ro‘yxatga olish')];
            $statusStep = [
                'draft' => 0, 'submitted' => 0, 'completeness_check' => 1, 'deficiency' => 1,
                'expert_review' => 2, 'authority_review' => 3, 'permit_issued' => 4, 'registered' => 4, 'rejected' => 1,
            ];
            $current = $statusStep[$application->status->value] ?? 0;
            $isRejected = $application->status->value === 'rejected';
            $done = $application->status->value === 'registered';
        @endphp
        <div class="card p-6">
            <ol class="flex items-center">
                @foreach ($steps as $i => $label)
                    @php
                        $isDone = $i < $current || ($i === $current && $done);
                        $isActive = $i === $current && ! $done;
                        $barDone = $i < $current;
                    @endphp
                    <li @class(['flex items-center', 'flex-1' => ! $loop->last])>
                        <div class="flex flex-col items-center gap-2">
                            <span @class([
                                'flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold ring-4 ring-white dark:ring-gray-900',
                                'bg-success-500 text-white' => $isDone,
                                'bg-brand-500 text-white' => $isActive && ! $isRejected,
                                'bg-error-500 text-white' => $isActive && $isRejected,
                                'bg-gray-100 text-gray-400 dark:bg-gray-800' => ! $isDone && ! $isActive,
                            ])>
                                @if($isDone)<x-icon name="verify" class="h-5 w-5" />@else{{ $i + 1 }}@endif
                            </span>
                            <span @class(['hidden text-center text-xs font-medium sm:block', 'text-gray-800 dark:text-gray-200' => $isDone || $isActive, 'text-gray-400' => ! $isDone && ! $isActive])>{{ $label }}</span>
                        </div>
                        @unless($loop->last)
                            <div @class(['mx-2 -mt-6 h-0.5 flex-1', 'bg-success-500' => $barDone, 'bg-gray-200 dark:bg-gray-700' => ! $barDone])></div>
                        @endunless
                    </li>
                @endforeach
            </ol>
            @if($isRejected)
                <p class="mt-4 text-center text-sm font-medium text-error-600">{{ __('Ariza rad etildi') }}</p>
            @endif
        </div>

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $application->company_name }}</h1>
                    <x-ui.badge :color="$application->status->color()">{{ $application->status->label() }}</x-ui.badge>
                </div>
                <p class="mt-1 font-mono text-sm text-gray-400">{{ $application->number }} · {{ $application->type->label() }}</p>
            </div>
        </div>

        {{-- Certificate preview with QR --}}
        @if($previewDoc)
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 bg-gradient-to-r from-brand-600 to-brand-800 px-6 py-4 text-white">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/15"><x-icon name="doc" class="h-5 w-5" /></span>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-white/70">Besqala Mining Valley</p>
                            <p class="font-semibold">{{ $previewDoc->kind->label() }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-white/15 px-2.5 py-0.5 text-xs font-medium">{{ $previewDoc->status->label() }}</span>
                </div>
                <div class="grid gap-6 p-6 sm:grid-cols-[1fr_auto]">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div><dt class="text-xs text-gray-400">{{ __('Egasi') }}</dt><dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</dd></div>
                        <div><dt class="text-xs text-gray-400">{{ __('Raqam') }}</dt><dd class="font-mono text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $previewDoc->number }}</dd></div>
                        <div><dt class="text-xs text-gray-400">{{ __('Berilgan sana') }}</dt><dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ optional($previewDoc->issued_at)->translatedFormat('d M Y') ?? '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-400">{{ __('Amal qilish muddati') }}</dt><dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ optional($previewDoc->expires_at)->translatedFormat('d M Y') ?? '—' }}</dd></div>
                        <div class="sm:col-span-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                            <a href="{{ route('app.documents.download', $previewDoc) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400"><x-icon name="doc" class="h-4 w-4" /> {{ __('Rasmiy hujjatni ochish — PDF') }}</a>
                        </div>
                    </dl>
                    <div class="text-center">
                        <img src="{{ $previewQr }}" alt="QR" class="mx-auto h-28 w-28">
                        <p class="mt-1 text-xs text-gray-400">{{ __('Haqiqiyligini tekshirish') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tabbed details --}}
        <div class="card" x-data="{ tab: 'info' }">
            <div class="flex gap-1 overflow-x-auto border-b border-gray-100 px-4 dark:border-gray-800">
                @foreach ([
                    'info' => __('Ma‘lumotlar'),
                    'founders' => __('Rahbariyat'),
                    'equipment' => __('Uskunalar'),
                    'docs' => __('Hujjatlar').' ('.$application->documents->count().')',
                ] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="whitespace-nowrap border-b-2 px-3 py-3 text-sm font-medium">{{ $label }}</button>
                @endforeach
            </div>

            <div class="p-6">
                {{-- Ma'lumotlar --}}
                <div x-show="tab === 'info'" class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            [__('STIR'), $application->stir ?? '—'],
                            [__('Tashkiliy shakl'), $application->legal_form],
                            [__('Hudud'), $application->region->label()],
                            [__('Manzil'), $application->address ?? '—'],
                            [__('Email'), $application->email ?? '—'],
                            [__('Telefon'), $application->phone ?? '—'],
                            [__('Bank hisob raqami'), $application->bank_account ?? '—'],
                            [__('Bank muassasasi'), $application->bank_name ?? '—'],
                            [__('BTC manzil'), $application->btc_address ?? '—'],
                            [__('Topshirilgan'), $application->submitted_at?->translatedFormat('d M Y') ?? '—'],
                        ] as [$k, $v])
                            <div><dt class="text-xs text-gray-400">{{ $k }}</dt><dd class="text-sm font-medium break-words text-gray-800 dark:text-gray-200">{{ $v }}</dd></div>
                        @endforeach
                    </dl>
                </div>

                {{-- Rahbariyat (founders) --}}
                <div x-show="tab === 'founders'" x-cloak class="space-y-2">
                    @forelse ($application->founders ?? [] as $founder)
                        <div class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3 dark:border-gray-800">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $founder['name'] ?? '' }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $founder['share'] ?? 0 }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">{{ __('Ma‘lumot yo‘q.') }}</p>
                    @endforelse
                </div>

                {{-- Uskunalar (equipment / location) --}}
                <div x-show="tab === 'equipment'" x-cloak>
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            [__('Mayning joylashuvi'), $application->location_address ?? '—'],
                            [__('Kadastr raqami'), $application->cadastre_number ?? '—'],
                            [__('Mulk huquqi'), $application->property_right ?? '—'],
                            [__('Quvvat'), $application->power_mw ? number_format((float)$application->power_mw, 1).' MW' : '—'],
                            [__('Energiya manbai'), $application->energy_source ?? '—'],
                        ] as [$k, $v])
                            <div><dt class="text-xs text-gray-400">{{ $k }}</dt><dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $v }}</dd></div>
                        @endforeach
                    </dl>
                </div>

                {{-- Hujjatlar --}}
                <div x-show="tab === 'docs'" x-cloak class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($application->documents as $doc)
                        <div class="flex items-center justify-between gap-3 py-3 first:pt-0">
                            <div class="min-w-0">
                                <span class="font-mono text-sm font-medium text-gray-700 dark:text-gray-300">{{ $doc->number }}</span>
                                <span class="ml-2 text-xs text-gray-400">{{ $doc->kind->label() }}</span>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <a href="{{ route('app.documents.download', $doc) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-brand-600 dark:text-gray-400"><x-icon name="doc" class="h-4 w-4" /> PDF</a>
                                <a href="{{ route('verify', ['no' => $doc->number]) }}" class="text-sm font-medium text-brand-600 dark:text-brand-400">{{ __('Tekshirish') }}</a>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-400">{{ __('Hujjatlar yo‘q.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Role-based workflow actions --}}
        @can('act', $application)
            <livewire:application-actions :application="$application" />
        @endcan

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Timeline --}}
            <div class="card">
                <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-800"><h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Harakatlar tarixi') }}</h2></div>
                <div class="p-6">
                    <ol class="relative space-y-5 border-l border-gray-200 pl-5 dark:border-gray-700">
                        @forelse ($application->events as $event)
                            <li class="relative">
                                <span class="absolute -left-[1.6rem] top-1 flex h-3 w-3 items-center justify-center rounded-full border-2 border-white bg-brand-500 dark:border-gray-900"></span>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $event->title }}</p>
                                @if($event->note)<p class="text-xs text-gray-500">{{ $event->note }}</p>@endif
                                <p class="text-xs text-gray-400">{{ $event->actor?->name }} · {{ $event->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400">{{ __('Harakatlar yo‘q.') }}</li>
                        @endforelse
                    </ol>
                </div>
            </div>

            {{-- Conclusions, deficiencies, documents --}}
            <div class="space-y-6">
                @if($application->conclusions->isNotEmpty())
                    <div class="card p-6">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">{{ __('Ekspert xulosalari') }}</h2>
                        @foreach ($application->conclusions as $conclusion)
                            <div class="mb-3 rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                                <x-ui.badge :color="$conclusion->verdict->color()">{{ $conclusion->verdict->label() }}</x-ui.badge>
                                @if($conclusion->comment)<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $conclusion->comment }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($application->deficiencies->isNotEmpty())
                    <div class="card p-6">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">{{ __('Kamchiliklar') }}</h2>
                        @foreach ($application->deficiencies as $deficiency)
                            <div class="rounded-xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-500/30 dark:bg-warning-500/10">
                                <ul class="list-inside list-disc space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                    @foreach ($deficiency->items as $item)<li>{{ $item }}</li>@endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
