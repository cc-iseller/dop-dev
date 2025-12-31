<x-filament-panels::page>
    <x-filament::section icon="heroicon-o-presentation-chart-line"  class="col-span-8">
        <x-slot name="heading">
            Analisis Pelaporan
        </x-slot>

        @livewire('pelaporan-page')
        @vite(['resources/css/app.css', 'resources/js/app.js'])  
    </x-filament::section>

    <x-filament::section icon="heroicon-o-banknotes"  class="col-span-8">
        <x-slot name="heading">
            Produk Terlaris
        </x-slot>
        @vite(['resources/css/app.css', 'resources/js/app.js'])  
    </x-filament::section>

    <x-filament::section icon="heroicon-o-banknotes"  class="col-span-8">
        <x-slot name="heading">
            Transaksi Harian
        </x-slot>
        @vite(['resources/css/app.css', 'resources/js/app.js'])  
    </x-filament::section>
</x-filament-panels::page>
