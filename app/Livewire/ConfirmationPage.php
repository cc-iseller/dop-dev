<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class ConfirmationPage extends Component
{
    public array $cart = [];
    public ?string $customerName = null;
    public string $paymentMethod = 'cash';
    public bool $isProcessing = false;
    public ?string $snapToken = null;

    /* =====================
       MOUNT
    ===================== */
    public function mount(): void
    {
        $this->cart = session('cart', []);

        if (empty($this->cart)) {
            redirect()->route('filament.admin.pages.cashier-page');
        }
    }

    /* =====================
       MAIN ACTION
    ===================== */
    public function confirmPayment(MidtransService $midtrans): void
    {
        if ($this->isProcessing) return;

        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang kosong')
                ->danger()
                ->send();
            return;
        }

        $stock = $this->validateStock();
        if (! $stock['valid']) {
            Notification::make()
                ->title('Stok Tidak Cukup')
                ->body($stock['message'])
                ->danger()
                ->send();
            return;
        }

        $this->isProcessing = true;

        try {
            if ($this->paymentMethod === 'cash') {
                $this->processCashPayment();
            } else {
                $this->processMidtransPayment($midtrans);
            }
        } finally {
            $this->isProcessing = false;
        }
    }

    /* =====================
       CASH PAYMENT
    ===================== */
    private function processCashPayment(): void
    {
        DB::transaction(function () {

            $transaction = $this->createTransaction('cash', 'paid');

            foreach ($this->cart as $item) {
                $this->createTransactionItem($transaction, $item);
                $this->updateStock($item);
            }
        });

        session()->forget('cart');

        Notification::make()
            ->title('Pembayaran Tunai Berhasil')
            ->success()
            ->send();

        redirect()->route('filament.admin.pages.cashier-page');
    }

    /* =====================
       MIDTRANS PAYMENT
    ===================== */
    private function processMidtransPayment(MidtransService $midtrans): void
    {
        DB::beginTransaction();

        try {
            $transaction = $this->createTransaction('midtrans', 'pending');

            foreach ($this->cart as $item) {
                // ❗ JANGAN update stok di sini
                $this->createTransactionItem($transaction, $item);
            }

            $snapToken = $midtrans->createSnapToken($transaction);

            $transaction->update([
                'snap_token' => $snapToken,
            ]);

            DB::commit();

            $this->snapToken = $snapToken;
            $this->dispatch('open-midtrans');

        } catch (\Throwable $e) {
            DB::rollBack();

            Notification::make()
                ->title('Gagal Memproses Midtrans')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /* =====================
       HELPERS
    ===================== */

    private function createTransaction(string $method, string $status): Transaction
    {
        return Transaction::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'customer_name' => $this->customerName,
            'payment_method' => $method,
            'status' => $status,
            'total_items' => array_sum(array_column($this->cart, 'qty')),
            'total_amount' => collect($this->cart)
                ->sum(fn ($i) => $i['price'] * $i['qty']),
        ]);
    }

    private function createTransactionItem(Transaction $transaction, array $item): void
    {
        $variantId = $item['variant_id'] ?? null;

        if ($variantId) {
            $variant = ProductVariant::with('product')->find($variantId);
            $sku = $variant?->sku;
            $variantSnapshot = [
                'size' => $variant?->size,
                'color' => $variant?->color,
            ];
        } else {
            $product = Product::find($item['product_id']);
            $sku = $product?->base_sku;
            $variantSnapshot = null;
        }

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $item['product_id'],
            'product_variant_id' => $variantId,
            'product_name_snapshot' => $item['name'],
            'sku_snapshot' => $sku,
            'variant_snapshot' => $variantSnapshot,
            'price' => $item['price'],
            'qty' => $item['qty'],
            'subtotal' => $item['price'] * $item['qty'],
        ]);
    }

    private function updateStock(array $item): void
    {
        if (!empty($item['variant_id'])) {
            ProductVariant::where('id', $item['variant_id'])
                ->decrement('stock', $item['qty']);
        } else {
            Product::where('id', $item['product_id'])
                ->decrement('base_stock', $item['qty']);
        }
    }

    private function validateStock(): array
    {
        foreach ($this->cart as $item) {
            $qty = $item['qty'];

            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant || $variant->stock < $qty) {
                    return ['valid' => false, 'message' => "Stok {$item['name']} tidak mencukupi"];
                }
            } else {
                $product = Product::find($item['product_id']);
                if (!$product || $product->base_stock < $qty) {
                    return ['valid' => false, 'message' => "Stok {$item['name']} tidak mencukupi"];
                }
            }
        }

        return ['valid' => true];
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd-His') . '-' . rand(100, 999);
    }

    public function goBack(): void
    {
        redirect()->route('filament.admin.pages.cashier-page');
    }

    public function render()
    {
        return view('livewire.confirmation-page');
    }
}
