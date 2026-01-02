<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class PelaporanPage extends Component
{
    public string $period = 'all'; // all, today, week, month

    public function render()
    {
        $statistics = $this->getStatistics();

        return view('livewire.pelaporan-page', [
            'statistics' => $statistics
        ]);
    }

    private function getStatistics()
    {
        // Query dasar untuk transactions
        $transactionQuery = Transaction::where('status', 'paid');

        // Filter berdasarkan periode
        $transactionQuery = $this->applyPeriodFilter($transactionQuery);

        // Hitung statistik transaksi
        $transactionStats = $transactionQuery->selectRaw('
            COUNT(*) as total_transactions,
            COALESCE(SUM(total_amount), 0) as total_revenue,
            COALESCE(AVG(total_amount), 0) as average_transaction
        ')->first();

        // Query untuk total item terjual
        $itemQuery = TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'paid');

        // Filter periode untuk items
        $itemQuery = $this->applyPeriodFilter($itemQuery, 'transactions');

        $totalItemsSold = $itemQuery->sum('transaction_items.qty');

        return [
            'total_transactions' => $transactionStats->total_transactions ?? 0,
            'total_revenue' => $transactionStats->total_revenue ?? 0,
            'average_transaction' => $transactionStats->average_transaction ?? 0,
            'total_items_sold' => $totalItemsSold ?? 0,
        ];
    }

    private function applyPeriodFilter($query, $table = 'transactions')
    {
        $createdAtColumn = $table . '.created_at';

        switch ($this->period) {
            case 'today':
                return $query->whereDate($createdAtColumn, today());
            case 'week':
                return $query->whereBetween($createdAtColumn, [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            case 'month':
                return $query->whereMonth($createdAtColumn, now()->month)
                             ->whereYear($createdAtColumn, now()->year);
            default: // 'all'
                return $query;
        }
    }

    public function setPeriod(string $period)
    {
        $this->period = $period;
    }
}