<x-filament::page>
    <div wire:poll>
        @if($this->availableUpdateVersion !== null)
            <div>
                A new update for Nox is available to download (version {{ $this->availableUpdateVersion }}.
            </div>
        @endif
    </div>

    {{ $this->form }}
</x-filament::page>
