<div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex flex-col justify-between h-full">

        <!-- HEADER -->
        <div>
            <h2 class="text-white font-semibold text-sm mb-4">
                Keranjang Belanja
            </h2>

            <!-- LIST ITEM CART -->
            <div class="space-y-3">

                @forelse ($cart as $item)
                <div class="flex justify-between items-center bg-gray-900 rounded-lg p-3">
                    <div>
                        <p class="text-white text-sm font-medium">
                            {{ $item['name'] }}
                        </p>
                        <p class="text-gray-400 text-xs">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- minus -->
                        <button wire:click="decreaseQty('{{ $item['key'] }}')"
                            class="px-2 py-1 bg-gray-700 text-white rounded hover:bg-gray-600">−</button>

                        <span class="text-white text-sm min-w-[20px] text-center">
                            {{ $item['qty'] }}
                        </span>

                        <!-- plus -->
                        <button wire:click="increaseQty('{{ $item['key'] }}')"
                            class="px-2 py-1 bg-gray-700 text-white rounded hover:bg-gray-600">+</button>

                        <!-- remove -->
                        <button wire:click="removeItem('{{ $item['key'] }}')"
                            class="ml-2 text-red-400 hover:text-red-500 text-sm">
                            ✕
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-400 text-sm py-8">
                    Keranjang masih kosong
                </div>
                @endforelse

            </div>

            <!-- SUMMARY -->
            @php
            $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);
            $total = $subtotal;
            @endphp

            <div class="mt-5 space-y-2 text-sm">
                <div class="flex justify-between text-gray-300">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="border-t border-gray-700 my-2"></div>

                <div class="flex justify-between text-white font-semibold">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTON -->
        <div class="mt-6 space-y-3">
            <button wire:click="goToConfirmation" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm
                {{ empty($cart) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ empty($cart) ? 'disabled' : '' }}>
                Bayar Sekarang
            </button>


            <button wire:click="clearCart" class="w-full bg-gray-700 hover:bg-gray-600 text-gray-200 py-2 rounded-lg text-sm
                {{ empty($cart) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ empty($cart) ? 'disabled' : '' }}>
                Kosongkan Keranjang
            </button>
        </div>

    </div>
</div>
