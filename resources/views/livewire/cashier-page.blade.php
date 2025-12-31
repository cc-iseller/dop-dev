<div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4">
        @foreach ($products as $product)
        @if (! $product->has_variants)
        <div class="bg-gray-800 border border-gray-600 rounded-xl p-4 flex flex-col justify-between">
            <div>
                <h2 class="text-white text-sm font-semibold"> {{ $product->name }} </h2>
                <p class="text-gray-400 text-xs"> {{ $product->category?->name }} </p>
                <span class="block text-yellow-400 font-bold mt-2 text-sm"> Rp {{ number_format($product->base_price, 0, ',', '.') }} </span>
                <p class="text-gray-400 text-xs mt-1"> Stok: {{ $product->base_stock }} </p>
            </div>
            <button wire:click="addToCart({{ $product->id }}, null)" class="mt-3 w-full bg-orange-400 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-lg">+ Keranjang</button>
        </div>

        @else
        @foreach ($product->variants->where('is_active', true) as $variant)

        <div class="bg-gray-800 border border-gray-600 rounded-xl p-4 flex flex-col justify-between">
            <div>
                <h2 class="text-white text-sm font-semibold">{{ $product->name }}</h2>
                <p class="text-gray-400 text-xs">{{ $product->category?->name }}</p>
                <p class="text-gray-400 text-xs">{{ $variant->options->pluck('name')->join(', ') }}</p>
                <span class="block text-yellow-400 font-bold mt-2 text-sm">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                <p class="text-gray-400 text-xs mt-1"> Stok: {{ $variant->stock }} </p>
            </div>
            <button wire:click="addToCart({{ $product->id }}, {{ $variant->id }})" class="mt-3 w-full bg-orange-400 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-lg">+ Keranjang</button>
        </div>
        @endforeach
        @endif
        @endforeach
    </div>
</div>
