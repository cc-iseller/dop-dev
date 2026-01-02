<div>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4">
        <div class="bg-gray-800 border border-gray-600 rounded-xl p-4">
            <p class="text-gray-400 text-xs">Total Transaksi</p>
            <h2 class="text-white text-2xl font-bold mt-1">
                {{ number_format($statistics['total_transactions'], 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-gray-800 border border-gray-600 rounded-xl p-4">
            <p class="text-gray-400 text-xs">Total Revenue</p>
            <h2 class="text-white text-2xl font-bold mt-1">
                Rp {{ number_format($statistics['total_revenue'], 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-gray-800 border border-gray-600 rounded-xl p-4">
            <p class="text-gray-400 text-xs">Rata-rata Transaksi</p>
            <h2 class="text-white text-2xl font-bold mt-1">
                Rp {{ number_format($statistics['average_transaction'], 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-gray-800 border border-gray-600 rounded-xl p-4">
            <p class="text-gray-400 text-xs">Total Item Terjual</p>
            <h2 class="text-white text-2xl font-bold mt-1">
                {{ number_format($statistics['total_items_sold'], 0, ',', '.') }}
            </h2>
        </div>
    </div>
</div>