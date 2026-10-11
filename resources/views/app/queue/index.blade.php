<x-layouts.app :title="__('Arizalar navbati')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Arizalar navbati')" :subtitle="__('To‘liqlik tekshiruvini kutayotgan arizalar')" />
        <x-app.application-table :applications="$applications" :empty="__('Navbatda ariza yo‘q.')" />
    </div>
</x-layouts.app>
