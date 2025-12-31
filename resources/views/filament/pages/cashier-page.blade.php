<x-filament-panels::page>
    <div class="grid grid-cols-12 gap-4">
    <x-filament::section icon="heroicon-o-shopping-bag"  class="col-span-8">
        <x-slot name="heading">
            Produk Tersedia
        </x-slot>

        @livewire('cashier-page')
        @vite(['resources/css/app.css', 'resources/js/app.js'])  
    </x-filament::section>

    <x-filament::section icon="heroicon-o-shopping-cart" class="col-span-4">
        <x-slot name="heading">
            Keranjang Belanja
        </x-slot>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])  
        <div id="cart-section"></div>
    </x-filament::section>
</x-filament-panels::page>
