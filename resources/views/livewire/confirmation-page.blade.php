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
                $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);
            @endphp

            <div class="border-t border-gray-700 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-300">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- PAYMENT METHOD -->
            <div class="space-y-2">
                <h3 class="text-white font-medium text-sm">
                    Metode Pembayaran
                </h3>

                <!-- CASH -->
                <label class="flex items-center gap-3 bg-gray-900 p-3 rounded-lg cursor-pointer">
                    <input
                        type="radio"
                        wire:model="paymentMethod"
                        value="cash"
                        class="text-blue-600"
                    >
                    <span class="text-white text-sm">Tunai (Cash)</span>
                </label>

                <!-- MIDTRANS -->
                <label class="flex items-center gap-3 bg-gray-900 p-3 rounded-lg cursor-pointer">
                    <input
                        type="radio"
                        wire:model="paymentMethod"
                        value="midtrans"
                        class="text-blue-600"
                    >
                    <span class="text-white text-sm">
                        Non Tunai (QRIS / VA / E-Wallet)
                    </span>
                </label>
            </div>

            <!-- ACTION -->
            <div class="flex gap-3">
                <button
                    wire:click="goBack"
                    type="button"
                    class="w-1/2 bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg text-sm"
                >
                    Kembali
                </button>

                <button
                    wire:click="confirmPayment"
                    wire:loading.attr="disabled"
                    wire:target="confirmPayment"
                    @disabled($isProcessing)
                    type="button"
                    class="w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="confirmPayment">
                        @if ($paymentMethod === 'cash')
                            Konfirmasi Pembayaran
                        @else
                            Bayar Sekarang
                        @endif
                    </span>

                    <span wire:loading wire:target="confirmPayment">
                        Memproses...
                    </span>
                </button>
            </div>

        </div>
    </div>

    <!-- MIDTRANS SNAP -->
    <script
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>

    <!-- SNAP HANDLER -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-midtrans', () => {
                const snapToken = @this.snapToken;

                if (!snapToken) {
                    alert('Snap token tidak tersedia');
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function () {
                        window.location.href = "{{ route('filament.admin.pages.cashier-page') }}";
                    },
                    onPending: function () {
                        alert('Menunggu pembayaran...');
                    },
                    onError: function () {
                        alert('Pembayaran gagal');
                    },
                    onClose: function () {
                        alert('Pembayaran dibatalkan');
                    }
                });
            });
        });
    </script>
</div>
