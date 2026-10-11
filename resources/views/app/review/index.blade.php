<x-layouts.app :title="__('Ko‘rib chiqish')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Ko‘rib chiqish navbati')" :subtitle="__('Ekspert xulosasini kutayotgan arizalar')" />
        <x-app.application-table :applications="$applications" :empty="__('Ko‘rib chiqish uchun ariza yo‘q.')" />
    </div>
</x-layouts.app>
