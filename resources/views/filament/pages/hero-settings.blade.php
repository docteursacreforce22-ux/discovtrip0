<x-filament-panels::page>

    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Enregistrer les images
            </x-filament::button>
        </div>
    </form>

</x-filament-panels::page>