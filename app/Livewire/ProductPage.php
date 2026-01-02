<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class ProductPage extends Component
{
    public string $period = 'all'; // all, today, week, month
    public int $limit = 10;

    public function render()
    {
        $bestSelling = $this->getBestSellingProducts();

        return view('livewire.product-page', [
            'products' => $bestSelling
        ]);
    }

    private function getBestSellingProducts()
    {
        $query = TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'paid')
            ->select([
                'transaction_items.product_id',
                'transaction_items.product_variant_id',
                DB::raw('COALESCE(transaction_items.product_name_snapshot, "") as product_name'),
                DB::raw('COALESCE(transaction_items.sku_snapshot, "") as sku'),
                DB::raw('SUM(transaction_items.qty) as total_sold'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue'),
            ]);

        switch ($this->period) {
            case 'today':
                $query->whereDate('transactions.created_at', today());
                break;
            case 'week':
                $query->whereBetween('transactions.created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('transactions.created_at', now()->month)
                      ->whereYear('transactions.created_at', now()->year);
                break;
        }

        $results = $query
            ->groupBy('transaction_items.product_id', 'transaction_items.product_variant_id', 'product_name', 'sku')
            ->orderByDesc('total_sold')
            ->limit($this->limit)
            ->get();

        // Ambil stok terkini dari database
        return $results->map(function ($item) {
            $currentStock = 0;

            if ($item->product_variant_id) {
                $variant = ProductVariant::find($item->product_variant_id);
                $currentStock = $variant->stock ?? 0;
            } else {
                $product = Product::find($item->product_id);
                $currentStock = $product->base_stock ?? 0;
            }

            return [
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'current_stock' => $currentStock,
                'total_sold' => $item->total_sold,
                'total_revenue' => $item->total_revenue,
            ];
        });
    }

    public function setPeriod(string $period)
    {
        $this->period = $period;
    }
}