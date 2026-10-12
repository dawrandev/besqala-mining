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

        <div class="card p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $application->company_name }}</h1>
                        <x-ui.badge :color="$application->status->color()">{{ $application->status->label() }}</x-ui.badge>
                    </div>
                    <p class="mt-1 font-mono text-sm text-gray-400">{{ $application->number }} · {{ $application->type->label() }}</p>
                </div>
            </div>
            <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    [__('STIR'), $application->stir ?? '—'],
                    [__('Hudud'), $application->region->label()],
                    [__('Tashkiliy shakl'), $application->legal_form],
                    [__('Quvvat'), $application->power_mw ? number_format((float)$application->power_mw, 1).' MW' : '—'],
                    [__('Energiya manbai'), $application->energy_source ?? '—'],
                    [__('Mayning joylashuvi'), $application->location_address ?? '—'],
                    [__('Bank hisob raqami'), $application->bank_account ?? '—'],
                    [__('Topshirilgan'), $application->submitted_at?->translatedFormat('d M Y') ?? '—'],
                ] as [$k, $v])
                    <div><dt class="text-xs text-gray-400">{{ $k }}</dt><dd class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $v }}</dd></div>
                @endforeach
            </dl>

            @if($application->founders)
                <div class="mt-6 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Ta‘sischilar') }}</p>
                    <div class="space-y-1">
                        @foreach ($application->founders as $founder)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">{{ $founder['name'] ?? '' }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $founder['share'] ?? 0 }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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

                @if($application->documents->isNotEmpty())
                    <div class="card p-6">
                        <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">{{ __('Hujjatlar') }}</h2>
                        @foreach ($application->documents as $doc)
                            <div class="flex items-center justify-between gap-3 py-2">
                                <div class="min-w-0">
                                    <span class="font-mono text-sm font-medium text-gray-700 dark:text-gray-300">{{ $doc->number }}</span>
                                    <span class="ml-2 text-xs text-gray-400">{{ $doc->kind->label() }}</span>
                                </div>
                                <div class="flex shrink-0 items-center gap-3">
                                    <a href="{{ route('app.documents.download', $doc) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-brand-600 dark:text-gray-400"><x-icon name="doc" class="h-4 w-4" /> PDF</a>
                                    <a href="{{ route('verify', ['no' => $doc->number]) }}" class="text-sm font-medium text-brand-600 dark:text-brand-400">{{ __('Tekshirish') }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
