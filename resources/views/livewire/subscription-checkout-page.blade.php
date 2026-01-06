<div>
    <div class="max-w-3xl mx-auto space-y-6">

    <div class="rounded-xl border border-gray-700 bg-gray-900 p-6">
        <h2 class="text-lg font-semibold text-white">Billing & Subscription</h2>
        <p class="text-sm text-gray-400">
            Upgrade paket toko untuk membuka fitur berbayar seperti Midtrans & Realtime Reports.
        </p>
    </div>

    {{-- Alert jika store belum kepilih --}}
    @if (! $store)
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">
            Store belum dipilih. Silakan pilih store (current store) dulu.
        </div>
    @else
        <div class="rounded-xl border border-gray-700 bg-gray-900 p-6 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-400">Store</p>
                    <p class="text-white font-semibold">{{ $store->name }}</p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-400">Paket aktif</p>
                    <p class="text-white font-semibold">
                        {{ $currentPlan?->name ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                {{-- FREE --}}
                <div class="rounded-xl border border-gray-700 bg-gray-950 p-5 space-y-3">
                    <div>
                        <h3 class="text-white font-semibold">Free</h3>
                        <p class="text-xs text-gray-400">Fitur dasar kasir & manajemen produk.</p>
                    </div>

                    <ul class="text-sm text-gray-300 space-y-1 list-disc pl-5">
                        <li>Cash / Debit manual</li>
                        <li>Manajemen Produk</li>
                        <li>Transaksi dasar</li>
                    </ul>

                    <button
                        type="button"
                        class="w-full rounded-lg bg-gray-700 text-white py-2 text-sm opacity-60 cursor-not-allowed"
                        disabled
                    >
                        Paket ini aktif (default)
                    </button>
                </div>

                {{-- PRO --}}
                <div class="rounded-xl border border-blue-500/30 bg-blue-500/10 p-5 space-y-3">
                    <div>
                        <h3 class="text-white font-semibold">Pro</h3>
                        <p class="text-xs text-blue-100/80">Buka fitur berbayar.</p>
                    </div>

                    <ul class="text-sm text-gray-100/90 space-y-1 list-disc pl-5">
                        <li>Midtrans Payment (QRIS/VA/E-Wallet)</li>
                        <li>Realtime Reports</li>
                    </ul>

                    <div class="text-sm text-gray-200">
                        <span class="text-gray-400">Harga:</span>
                        <span class="font-semibold">Rp {{ number_format($proPrice, 0, ',', '.') }}/bulan</span>
                    </div>

                    @if ($isPro)
                        <button
                            type="button"
                            class="w-full rounded-lg bg-blue-700 text-white py-2 text-sm opacity-60 cursor-not-allowed"
                            disabled
                        >
                            Sudah Pro
                        </button>
                    @else
                        <button
                            wire:click="upgradeToPro"
                            wire:loading.attr="disabled"
                            wire:target="upgradeToPro"
                            type="button"
                            class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-2 text-sm font-semibold disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="upgradeToPro">Upgrade & Bayar</span>
                            <span wire:loading wire:target="upgradeToPro">Memproses...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Midtrans Snap --}}
    <script
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-midtrans', ({ token, redirectUrl }) => {
                if (!token) {
                    alert('Snap token tidak tersedia');
                    return;
                }

                window.snap.pay(token, {
                    onSuccess: function () {
                        if (redirectUrl) window.location.href = redirectUrl;
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

</div>
