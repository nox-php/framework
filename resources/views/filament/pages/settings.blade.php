<x-filament::page>
    <div wire:poll>
        @if($this->availableUpdateVersion !== null)
            <x-nox::filament.settings-banner>
                A new update for Nox is available to download (v{{ $this->availableUpdateVersion }}).
            </x-nox::filament.settings-banner>
        @endif
    </div>

    {{ $this->form }}
</x-filament::page>
