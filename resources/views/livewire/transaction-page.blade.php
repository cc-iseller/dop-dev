<div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-white font-semibold text-sm">
                Transaksi Terbaru
            </h2>
            <p class="text-gray-400 text-xs">
                Daftar transaksi terbaru yang masuk
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-400 font-medium">
                            Kode Transaksi
                        </th>
                        <th class="px-4 py-3 text-left text-gray-400 font-medium">
                            Tanggal
                        </th>
                        <th class="px-4 py-3 text-left text-gray-400 font-medium">
                            Metode Pembayaran
                        </th>
                        <th class="px-4 py-3 text-right text-gray-400 font-medium">
                            Total Pesanan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse ($transactions as $trx)
                        <tr class="hover:bg-gray-700/40">
                            <td class="px-4 py-3 text-blue-400 font-medium">
                                {{ $trx->invoice_number }}
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                {{ $trx->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $paymentDetail = \App\Http\Controllers\MidtransWebhookController::getPaymentDetail($trx);

                                    $badgeColor = match ($trx->payment_method) {
                                        'cash' => 'bg-green-600/20 text-green-400',
                                        'qris' => 'bg-blue-600/20 text-blue-400',
                                        'transfer' => 'bg-purple-600/20 text-purple-400',
                                        'debit' => 'bg-yellow-600/20 text-yellow-400',
                                        'midtrans' => 'bg-pink-600/20 text-pink-400',
                                        default => 'bg-gray-600/20 text-gray-400',
                                    };
                                @endphp
                                
                                <span class="px-2 py-1 rounded text-xs {{ $badgeColor }}">
                                    {{ $paymentDetail }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-white font-semibold">
                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                Belum ada transaksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>