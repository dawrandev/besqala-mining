<x-layouts.app :title="__('Yangi ariza')">
    <div class="mx-auto max-w-3xl">
        <x-app.page-header :title="__('Yangi ariza')" :subtitle="__('Rezidentlikka ariza topshirish')" />

        <form x-data="applicationForm()" x-ref="form" @submit.prevent="save(true)" class="space-y-6">
            <div class="card p-6">
                <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">{{ __('Korxona ma‘lumotlari') }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Korxona nomi') }} <span class="text-error-500">*</span></label>
                        <input type="text" name="company_name" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <p data-error="company_name" class="mt-1 hidden text-xs text-error-500"></p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('STIR') }} <span class="text-error-500">*</span></label>
                        <input type="text" name="stir" maxlength="9" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <p data-error="stir" class="mt-1 hidden text-xs text-error-500"></p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Hudud') }} <span class="text-error-500">*</span></label>
                        <select name="region" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">{{ __('Tanlang') }}</option>
                            @foreach (\App\Enums\Region::options() as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        </select>
                        <p data-error="region" class="mt-1 hidden text-xs text-error-500"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Manzil') }} <span class="text-error-500">*</span></label>
                        <input type="text" name="address" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <p data-error="address" class="mt-1 hidden text-xs text-error-500"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mayning joylashuvi') }}</label>
                        <input type="text" name="location_address" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kadastr raqami') }}</label>
                        <input type="text" name="cadastre_number" placeholder="35:05:0106003:78" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mulk huquqi') }}</label>
                        <input type="text" name="property_right" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                        <input type="email" name="email" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <p data-error="email" class="mt-1 hidden text-xs text-error-500"></p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Telefon') }}</label>
                        <input type="text" name="phone" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-white">{{ __('Ta‘sischilar') }}</h2>
                    <button type="button" @click="addFounder()" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 dark:text-brand-400"><x-icon name="plus" class="h-4 w-4" /> {{ __('Qo‘shish') }}</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(f, i) in founders" :key="i">
                        <div class="flex items-center gap-3">
                            <input type="text" :name="`founders[${i}][name]`" x-model="f.name" placeholder="{{ __('F.I.Sh. yoki tashkilot') }}" class="flex-1 rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <input type="number" :name="`founders[${i}][share]`" x-model="f.share" placeholder="%" class="w-20 rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <button type="button" x-show="founders.length > 1" @click="removeFounder(i)" class="rounded-lg p-2 text-gray-400 hover:bg-error-50 hover:text-error-500 dark:hover:bg-error-500/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">{{ __('Quvvat va energiya') }}</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Quvvat (MW)') }}</label>
                        <input type="number" step="0.01" name="power_mw" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Energiya manbai') }}</label>
                        <input type="text" name="energy_source" placeholder="{{ __('Quyosh / Gidro') }}" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bank hisob raqami') }}</label>
                        <input type="text" name="bank_account" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bank muassasasi') }}</label>
                        <input type="text" name="bank_name" placeholder="«Mikrokreditbank»" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('BTC manzil') }}</label>
                        <input type="text" name="btc_address" placeholder="bc1q..." class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" :disabled="submitting" @click="save(false)" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-60 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/5">{{ __('Qoralama saqlash') }}</button>
                <button type="submit" :disabled="submitting" class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-theme-sm hover:bg-brand-600 disabled:opacity-60">{{ __('Topshirish') }}</button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function applicationForm() {
                return {
                    founders: [{ name: '', share: 100 }],
                    submitting: false,
                    addFounder() { this.founders.push({ name: '', share: 0 }); },
                    removeFounder(i) { this.founders.splice(i, 1); },
                    async save(doSubmit) {
                        const { http, toast, showErrors, clearErrors, ValidationError } = window.besqala;
                        const form = this.$refs.form;
                        const fd = new FormData(form);
                        fd.set('submit', doSubmit ? '1' : '0');
                        this.submitting = true;
                        clearErrors(form);
                        try {
                            const res = await http.post(@js(route('app.applications.store')), fd);
                            window.location = res.redirect;
                        } catch (e) {
                            this.submitting = false;
                            if (e instanceof ValidationError) {
                                showErrors(form, e.errors);
                                toast(@js(__('Maydonlarni tekshiring.')), 'error');
                            } else {
                                toast(e.message, 'error');
                            }
                        }
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
