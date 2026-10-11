<x-layouts.app :title="__('Ruxsatnomalar')">
    <div class="mx-auto max-w-7xl">
        <x-app.page-header :title="__('Ruxsatnoma navbati')" :subtitle="__('Ruxsatnoma qarorini kutayotgan arizalar')" />
        <x-app.application-table :applications="$applications" :empty="__('Navbatda ariza yo‘q.')" />
    </div>
</x-layouts.app>
