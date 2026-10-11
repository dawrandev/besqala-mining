<x-layouts.app :title="__('Kontent boshqaruvi')">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Kontent boshqaruvi') }}</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ __('Sayt kontentini boshqarish — rahbariyat, biz haqimizda, yangiliklar') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-ui.stat :label="__('Yangiliklar')" :value="$newsCount" :caption="__('chop etilgan/qoralama')" icon="news" color="brand" />
            <x-ui.stat :label="__('Rahbariyat')" :value="$leadersCount" :caption="__('a‘zolar')" icon="users" color="indigo" />
            <x-ui.stat :label="__('Biz haqimizda')" :value="$aboutFilled ? __('To‘ldirilgan') : __('Bo‘sh')" :caption="__('sahifa holati')" icon="info" :color="$aboutFilled ? 'success' : 'warning'" />
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            @foreach ([
                ['app.admin.news', __('Yangiliklar'), __('Yangiliklarni qo‘shish va tahrirlash'), 'news'],
                ['app.admin.leadership', __('Rahbariyat'), __('Rahbariyat a‘zolarini boshqarish'), 'users'],
                ['app.admin.about', __('Biz haqimizda'), __('Sahifa matni va statistikasi'), 'info'],
            ] as [$route, $title, $desc, $icon])
                <a href="{{ route($route) }}" class="card group p-6 transition hover:shadow-theme-md">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400"><x-icon :name="$icon" class="h-6 w-6" /></span>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 group-hover:gap-2 dark:text-brand-400">{{ __('Ochish') }} <x-icon name="arrow-right" class="h-4 w-4 transition-all" /></span>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
