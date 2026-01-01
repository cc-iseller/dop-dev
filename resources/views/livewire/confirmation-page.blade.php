<div>
    <div class="max-w-3xl mx-auto">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">

            <!-- HEADER -->
            <div>
                <h2 class="text-white font-semibold text-lg">
                    Konfirmasi Pesanan
                </h2>
                <p class="text-gray-400 text-sm">
                    Periksa kembali pesanan sebelum melanjutkan pembayaran
                </p>
            </div>

            <!-- CART LIST -->
            <div class="space-y-3">
                @foreach ($cart as $item)
                    <div class="flex justify-between items-center bg-gray-900 rounded-lg p-4">
                        <div>
                            <p class="text-white text-sm font-medium">
                                {{ $item['name'] }}
                            </p>
                            <p class="text-gray-400 text-xs">
                                {{ $item['qty'] }} × Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="text-white text-sm font-semibold">
                            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SUMMARY -->
            @php
                $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);
                $total = $subtotal;
            @endphp

            <div class="border-t border-gray-700 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-300">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-white font-semibold text-base">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- PAYMENT METHOD (DUMMY) -->
            <div class="space-y-2">
                <h3 class="text-white font-medium text-sm">
                    Metode Pembayaran
                </h3>

                <div class="space-y-2">
                    <label class="flex items-center gap-3 bg-gray-900 p-3 rounded-lg cursor-pointer">
                        <input type="radio" checked class="text-blue-600">
                        <span class="text-white text-sm">Tunai</span>
                    </label>

                    <label class="flex items-center gap-3 bg-gray-900 p-3 rounded-lg cursor-pointer opacity-50">
                        <input type="radio" disabled>
                        <span class="text-white text-sm">QRIS (Coming Soon)</span>
                    </label>

                    <label class="flex items-center gap-3 bg-gray-900 p-3 rounded-lg cursor-pointer opacity-50">
                        <input type="radio" disabled>
                        <span class="text-white text-sm">Transfer Bank (Coming Soon)</span>
                    </label>
                </div>
            </div>

             <!-- ACTION -->
            <div class="flex gap-3">
                <a href="" wire:click.prevent="goBack" class="w-1/2 text-center bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg text-sm">
                    Kembali
                </a>

                <button 
                    wire:click="confirmPayment" 
                    wire:loading.attr="disabled"
                    @if($isProcessing) disabled @endif
                    class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="confirmPayment">
                        Konfirmasi & Bayar
                    </span>
                    <span wire:loading wire:target="confirmPayment">
                        Memproses...
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>