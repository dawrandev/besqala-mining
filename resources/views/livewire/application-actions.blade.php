<div class="card border-brand-200 p-6 dark:border-brand-500/30" x-data="{ panel: null }">
    <h2 class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
        <x-icon name="shield" class="h-5 w-5 text-brand-500" /> {{ __('Amallar') }}
    </h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Joriy holat') }}: <span class="font-medium">{{ $application->status->label() }}</span></p>

    <div class="mt-4 flex flex-wrap gap-3">
        @switch($application->status)
            @case(\App\Enums\ApplicationStatus::Draft)
                <button wire:click="submitDraft" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Topshirish') }}</button>
                @break

            @case(\App\Enums\ApplicationStatus::Submitted)
            @case(\App\Enums\ApplicationStatus::CompletenessCheck)
            @case(\App\Enums\ApplicationStatus::Deficiency)
                <button wire:click="sendToExpert" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Ekspertizaga yuborish') }}</button>
                <button @click="panel = panel === 'deficiency' ? null : 'deficiency'" class="rounded-xl border border-warning-300 px-5 py-2.5 text-sm font-semibold text-warning-700 hover:bg-warning-50 dark:border-warning-500/40 dark:text-warning-500">{{ __('Kamchilik xabarnomasi') }}</button>
                <button @click="panel = panel === 'reject' ? null : 'reject'" class="rounded-xl border border-error-300 px-5 py-2.5 text-sm font-semibold text-error-700 hover:bg-error-50 dark:border-error-500/40 dark:text-error-500">{{ __('Rad etish') }}</button>
                @break

            @case(\App\Enums\ApplicationStatus::ExpertReview)
                <button wire:click="conclude('positive')" class="rounded-xl bg-success-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-success-700">{{ __('Ijobiy xulosa') }}</button>
                <button wire:click="conclude('negative')" class="rounded-xl border border-error-300 px-5 py-2.5 text-sm font-semibold text-error-700 hover:bg-error-50 dark:border-error-500/40 dark:text-error-500">{{ __('Salbiy xulosa') }}</button>
                @break

            @case(\App\Enums\ApplicationStatus::AuthorityReview)
                <button wire:click="issuePermit" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('Ruxsatnoma berish') }}</button>
                <button @click="panel = panel === 'reject' ? null : 'reject'" class="rounded-xl border border-error-300 px-5 py-2.5 text-sm font-semibold text-error-700 hover:bg-error-50 dark:border-error-500/40 dark:text-error-500">{{ __('Rad etish') }}</button>
                @break

            @default
                <p class="text-sm text-gray-400">{{ __('Bu bosqichda amal mavjud emas.') }}</p>
        @endswitch
    </div>

    {{-- Deficiency panel --}}
    <div x-show="panel === 'deficiency'" x-cloak x-transition class="mt-4">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kamchiliklar (har bir qatorda bittadan)') }}</label>
        <textarea wire:model="deficiencyText" rows="3" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
        @error('deficiencyText')<p class="mt-1 text-xs text-error-500">{{ $message }}</p>@enderror
        <button wire:click="sendDeficiency" class="mt-3 rounded-xl bg-warning-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-warning-700">{{ __('Yuborish') }}</button>
    </div>

    {{-- Reject panel --}}
    <div x-show="panel === 'reject'" x-cloak x-transition class="mt-4">
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Rad etish sababi') }}</label>
        <textarea wire:model="comment" rows="2" class="w-full rounded-lg border-gray-300 text-sm shadow-theme-xs focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
        <button wire:click="reject" class="mt-3 rounded-xl bg-error-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-error-700">{{ __('Rad etishni tasdiqlash') }}</button>
    </div>
</div>
