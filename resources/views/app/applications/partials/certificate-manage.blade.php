{{-- Certificate lifecycle management (Directorate). Each change needs a reason. --}}
@php
    $certEmail = $certificate->resident?->email ?? ($certificate->data_snapshot['email'] ?? null);
    $lastDelivery = $certificate->deliveries->first();
@endphp
<div class="card border-warning-200 p-6 dark:border-warning-500/30"
     x-data="certificateManage(@js(route('app.documents.status', $certificate)), @js(route('app.documents.email', $certificate)))">
    <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
        <x-icon name="shield" class="h-5 w-5 text-warning-500" /> {{ __('Guvohnoma boshqaruvi') }}
    </h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        <span class="font-mono font-medium">{{ $certificate->number }}</span> ·
        <x-ui.badge :color="$certificate->status->color()">{{ $certificate->status->label() }}</x-ui.badge>
    </p>

    {{-- Send the certificate to the resident (opens the staff member's mail client) --}}
    <div class="mt-4 flex flex-wrap items-center gap-3 rounded-xl bg-gray-50 px-4 py-3 dark:bg-white/5">
        @if ($certEmail)
            <button type="button" @click="sendEmail()" :disabled="emailing"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600 disabled:opacity-60">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg>
                {{ __('Rezidentga yuborish (email)') }}
            </button>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $certEmail }}</span>
        @else
            <span class="text-xs text-gray-400">{{ __('Rezident elektron pochtasi ko‘rsatilmagan.') }}</span>
        @endif
        @if ($lastDelivery)
            <span class="text-xs text-gray-400">· {{ __('Oxirgi yuborilgan') }}: {{ $lastDelivery->created_at?->translatedFormat('d M Y, H:i') }}</span>
        @endif
    </div>

    <div class="mt-4 flex flex-wrap gap-3">
        @if ($certificate->status === \App\Enums\DocumentStatus::Active)
            <button type="button" @click="open('suspend')" class="rounded-xl border border-warning-300 px-4 py-2 text-sm font-semibold text-warning-700 hover:bg-warning-50 dark:border-warning-500/40 dark:text-warning-500">{{ __('To‘xtatib turish') }}</button>
        @endif
        @if ($certificate->status === \App\Enums\DocumentStatus::Suspended)
            <button type="button" @click="open('restore')" class="rounded-xl border border-success-300 px-4 py-2 text-sm font-semibold text-success-700 hover:bg-success-50 dark:border-success-500/40 dark:text-success-500">{{ __('Tiklash') }}</button>
        @endif
        <button type="button" @click="open('reissue')" class="rounded-xl border border-brand-300 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-500/40 dark:text-brand-400">{{ __('Qayta rasmiylashtirish') }}</button>
        <button type="button" @click="open('terminate')" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">{{ __('Amalni tugatish') }}</button>
        <button type="button" @click="open('annul')" class="rounded-xl border border-error-300 px-4 py-2 text-sm font-semibold text-error-700 hover:bg-error-50 dark:border-error-500/40 dark:text-error-500">{{ __('Guvohnomani bekor qilish') }}</button>
    </div>

    <div x-show="panel" x-cloak x-transition class="mt-4">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300" x-text="labels[panel] ?? @js(__('Asos'))"></label>
        <textarea x-model="reason" rows="2" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
        <p data-error="reason" class="mt-1 hidden text-xs text-error-500"></p>
        <div class="mt-3 flex gap-3">
            <button type="button" @click="run()" :disabled="busy" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 disabled:opacity-60">{{ __('Tasdiqlash') }}</button>
            <button type="button" @click="panel = null" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">{{ __('Bekor qilish') }}</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function certificateManage(url, emailUrl) {
            return {
                panel: null,
                reason: '',
                busy: false,
                emailing: false,
                emailUrl,
                async sendEmail() {
                    const { http, toast } = window.besqala;
                    this.emailing = true;
                    try {
                        const res = await http.post(this.emailUrl, {});
                        toast(res.message);
                        window.location.href = res.mailto;
                    } catch (e) {
                        toast(e.message, 'error');
                    } finally {
                        this.emailing = false;
                    }
                },
                labels: {
                    suspend: @js(__('To‘xtatib turish sababi')),
                    restore: @js(__('Tiklash sababi')),
                    terminate: @js(__('Amalni tugatish sababi')),
                    annul: @js(__('Bekor qilish sababi')),
                    reissue: @js(__('Qayta rasmiylashtirish sababi')),
                },
                open(action) {
                    this.panel = action;
                    this.reason = '';
                    window.besqala.clearErrors(this.$root);
                },
                async run() {
                    const { http, toast, showErrors, clearErrors, ValidationError } = window.besqala;
                    clearErrors(this.$root);
                    this.busy = true;
                    try {
                        const res = await http.post(url, { action: this.panel, reason: this.reason });
                        window.location = res.redirect;
                    } catch (e) {
                        this.busy = false;
                        if (e instanceof ValidationError) {
                            showErrors(this.$root, e.errors);
                        } else {
                            toast(e.message, 'error');
                        }
                    }
                },
            };
        }
    </script>
@endpush
