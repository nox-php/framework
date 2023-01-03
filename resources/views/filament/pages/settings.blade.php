<x-filament::page>
    <div wire:poll>
        @if($this->availableUpdateVersion !== null)
            <x-nox::filament.settings-banner>
                A new update for Nox is available to install (v{{ $this->availableUpdateVersion }}).
            </x-nox::filament.settings-banner>
        @endif
    </div>

    <form wire:submit.prevent="save" class="filament-form space-y-6">
        {{ $this->form }}

        <div class="filament-page-actions flex flex-wrap items-center gap-4 justify-start filament-form-actions">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                Save
            </x-filament::button>

            <x-filament::button tag="a" href="#" color="secondary">
                Cancel
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
