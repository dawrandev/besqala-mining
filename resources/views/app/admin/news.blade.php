<x-layouts.app :title="__('Yangiliklar')">
    <div class="mx-auto max-w-7xl" x-data="newsManager()">
        <x-app.page-header :title="__('Yangiliklar')" :subtitle="__('Yangiliklarni qo‘shish va tahrirlash')" />

        <div class="mb-4 flex justify-end">
            <button type="button" @click="open()" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600"><x-icon name="plus" class="h-4 w-4" /> {{ __('Yangilik qo‘shish') }}</button>
        </div>

        <div x-ref="list" data-ajax-list>
            @include('app.admin.partials.news-table')
        </div>

        {{-- Modal --}}
        <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50" @click="show = false"></div>
            <div x-ref="modal" class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-theme-xl dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editingId ? @js(__('Yangilikni tahrirlash')) : @js(__('Yangi yangilik'))"></h3>

                <div class="mt-4 inline-flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
                    @foreach (['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $lbl)
                        <button type="button" @click="loc = '{{ $code }}'" :class="loc === '{{ $code }}' ? 'bg-white text-gray-900 shadow-theme-xs dark:bg-gray-700 dark:text-white' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-semibold">{{ $lbl }}</button>
                    @endforeach
                </div>

                <div class="mt-4 space-y-4">
                    @foreach (['uz', 'ru', 'en'] as $code)
                        <div x-show="loc === '{{ $code }}'" class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sarlavha') }} ({{ strtoupper($code) }})</label>
                                <input type="text" x-model="form.title.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @if($code === 'uz')<p data-error="title.uz" class="mt-1 hidden text-xs text-error-500"></p>@endif
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Qisqa matn') }} ({{ strtoupper($code) }})</label>
                                <textarea x-model="form.excerpt.{{ $code }}" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Matn') }} ({{ strtoupper($code) }})</label>
                                <textarea x-model="form.body.{{ $code }}" rows="5" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                                @if($code === 'uz')<p data-error="body.uz" class="mt-1 hidden text-xs text-error-500"></p>@endif
                            </div>
                        </div>
                    @endforeach

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kategoriya') }}</label>
                        <select x-model="form.category" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @foreach (\App\Enums\NewsCategory::cases() as $cat)
                                <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" x-model="form.published" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500"> {{ __('Chop etish') }}
                    </label>
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
            function newsManager() {
                const blank = () => ({
                    title: { uz: '', ru: '', en: '' },
                    excerpt: { uz: '', ru: '', en: '' },
                    body: { uz: '', ru: '', en: '' },
                    category: 'official',
                    published: true,
                });
                return {
                    show: false,
                    editingId: null,
                    busy: false,
                    loc: 'uz',
                    form: blank(),
                    urls: {
                        store: @js(route('app.admin.news.store')),
                        list: @js(route('app.admin.news.list')),
                        base: @js(url('app/admin/news')),
                    },
                    init() {
                        this.$refs.list.addEventListener('click', (e) => {
                            const edit = e.target.closest('[data-edit]');
                            if (edit) { this.edit(edit.dataset.edit); return; }
                            const del = e.target.closest('[data-delete]');
                            if (del) { this.destroy(del.dataset.delete); return; }
                            const link = e.target.closest('a');
                            if (link && this.$refs.list.contains(link)) { e.preventDefault(); this.loadPage(link.href); }
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
                        this.form = { title: data.title, excerpt: data.excerpt, body: data.body, category: data.category, published: data.published };
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
                    async loadPage(url) {
                        this.$refs.list.innerHTML = await window.besqala.http.get(url);
                    },
                    async reload() { await this.loadPage(this.urls.list); },
                };
            }
        </script>
    @endpush
</x-layouts.app>
