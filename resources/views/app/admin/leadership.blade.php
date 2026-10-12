<x-layouts.app :title="__('Rahbariyat')">
    <div class="mx-auto max-w-7xl" x-data="leadershipManager()">
        <x-app.page-header :title="__('Rahbariyat')" :subtitle="__('Rahbariyat a‘zolarini boshqarish')" />

        <div class="mb-4 flex justify-end">
            <button type="button" @click="open()" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600"><x-icon name="plus" class="h-4 w-4" /> {{ __('A‘zo qo‘shish') }}</button>
        </div>

        <div x-ref="list" data-ajax-list>
            @include('app.admin.partials.leadership-grid')
        </div>

        {{-- Modal --}}
        <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50" @click="show = false"></div>
            <div x-ref="modal" class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-theme-xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editingId ? @js(__('A‘zoni tahrirlash')) : @js(__('Yangi a‘zo'))"></h3>

                <div class="mt-4 inline-flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
                    @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $lbl)
                        <button type="button" @click="loc = '{{ $code }}'" :class="loc === '{{ $code }}' ? 'bg-white text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-semibold">{{ $lbl }}</button>
                    @endforeach
                </div>

                <div class="mt-4 space-y-4">
                    @foreach (['uz', 'ru', 'en'] as $code)
                        <div x-show="loc === '{{ $code }}'" class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('F.I.Sh.') }} ({{ strtoupper($code) }})</label>
                                <input type="text" x-model="form.full_name.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @if($code === 'uz')<p data-error="full_name.uz" class="mt-1 hidden text-xs text-error-500"></p>@endif
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lavozim') }} ({{ strtoupper($code) }})</label>
                                <input type="text" x-model="form.position.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @if($code === 'uz')<p data-error="position.uz" class="mt-1 hidden text-xs text-error-500"></p>@endif
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bo‘lim') }} ({{ strtoupper($code) }})</label>
                                <input type="text" x-model="form.department.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Biografiya') }} ({{ strtoupper($code) }})</label>
                                <textarea x-model="form.bio.{{ $code }}" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                        </div>
                    @endforeach
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tartib raqami') }}</label>
                        <input type="number" x-model="form.sort_order" class="w-32 rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="show = false" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">{{ __('Bekor qilish') }}</button>
                    <button type="button" @click="save()" :disabled="busy" class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 disabled:opacity-60">{{ __('Saqlash') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function leadershipManager() {
                const blank = () => ({
                    full_name: { uz: '', ru: '', en: '' },
                    position: { uz: '', ru: '', en: '' },
                    department: { uz: '', ru: '', en: '' },
                    bio: { uz: '', ru: '', en: '' },
                    sort_order: 0,
                });
                return {
                    show: false,
                    editingId: null,
                    busy: false,
                    loc: 'uz',
                    form: blank(),
                    urls: {
                        store: @js(route('app.admin.leadership.store')),
                        list: @js(route('app.admin.leadership.list')),
                        base: @js(url('app/admin/leadership')),
                    },
                    init() {
                        this.$refs.list.addEventListener('click', (e) => {
                            const edit = e.target.closest('[data-edit]');
                            if (edit) { this.edit(edit.dataset.edit); return; }
                            const del = e.target.closest('[data-delete]');
                            if (del) { this.destroy(del.dataset.delete); return; }
                        });
                    },
                    open() {
                        this.form = blank();
                        this.editingId = null;
                        this.loc = 'uz';
                        this.show = true;
                    },
                    async edit(id) {
                        const data = await window.besqala.http.get(`${this.urls.base}/${id}/edit`);
                        this.form = { full_name: data.full_name, position: data.position, department: data.department, bio: data.bio, sort_order: data.sort_order };
                        this.editingId = id;
                        this.loc = 'uz';
                        this.show = true;
                    },
                    async save() {
                        const { http, toast, showErrors, clearErrors, ValidationError } = window.besqala;
                        clearErrors(this.$refs.modal);
                        this.busy = true;
                        try {
                            const url = this.editingId ? `${this.urls.base}/${this.editingId}` : this.urls.store;
                            const res = this.editingId ? await http.put(url, this.form) : await http.post(url, this.form);
                            this.show = false;
                            toast(res.message);
                            await this.reload();
                        } catch (e) {
                            if (e instanceof ValidationError) { showErrors(this.$refs.modal, e.errors); }
                            else { toast(e.message, 'error'); }
                        } finally {
                            this.busy = false;
                        }
                    },
                    async destroy(id) {
                        if (!confirm(@js(__('O‘chirishni tasdiqlaysizmi?')))) return;
                        const { http, toast } = window.besqala;
                        try {
                            const res = await http.delete(`${this.urls.base}/${id}`);
                            toast(res.message);
                            await this.reload();
                        } catch (e) { toast(e.message, 'error'); }
                    },
                    async reload() {
                        this.$refs.list.innerHTML = await window.besqala.http.get(this.urls.list);
                    },
                };
            }
        </script>
    @endpush
</x-layouts.app>
