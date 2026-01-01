<div>
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-white font-semibold text-sm">
                Produk Terlaris
            </h2>
            <p class="text-gray-400 text-xs">
                Daftar produk dengan penjualan tertinggi
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-400 font-medium">
                            Produk
                        </th>
                        <th class="px-4 py-3 text-left text-gray-400 font-medium">
                            SKU
                        </th>
                        <th class="px-4 py-3 text-right text-gray-400 font-medium">
                            Stok
                        </th>
                        <th class="px-4 py-3 text-right text-gray-400 font-medium">
                            Terjual
                        </th>
                        <th class="px-4 py-3 text-right text-gray-400 font-medium">
                            Total Uang
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-700">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-700/40">
                            <td class="px-4 py-3 text-white font-medium">
                                {{ $product['product_name'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                {{ $product['sku'] }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-300">
                                {{ number_format($product['current_stock'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-300">
                                {{ number_format($product['total_sold'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right text-green-400 font-semibold">
                                Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                Belum ada data penjualan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>