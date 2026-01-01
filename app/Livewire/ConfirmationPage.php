<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class ConfirmationPage extends Component
{
    public array $cart = [];
    public ?string $customerName = null;
    public bool $isProcessing = false;

    public function mount()
    {
        $this->cart = session('cart', []);

        if (empty($this->cart)) {
            return redirect()->route('filament.admin.pages.cashier-page');
        }
    }

    public function confirmPayment()
    {
        // Antisipasi duplikasi - cek jika sedang proses
        if ($this->isProcessing) {
            return;
        }

        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang kosong')
                ->danger()
                ->send();
            return;
        }

        // Set flag processing
        $this->isProcessing = true;

        try {
            // Validasi stok terlebih dahulu
            $stockValidation = $this->validateStock();
            if (!$stockValidation['valid']) {
                $this->isProcessing = false;
                Notification::make()
                    ->title('Stok Tidak Mencukupi')
                    ->body($stockValidation['message'])
                    ->danger()
                    ->send();
                return;
            }

            DB::beginTransaction();

            $totalItems = array_sum(array_column($this->cart, 'qty'));
            $totalAmount = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);

            $transaction = Transaction::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_name' => $this->customerName,
                'payment_method' => 'cash',
                'status' => 'paid',
                'total_items' => $totalItems,
                'total_amount' => $totalAmount,
            ]);

            // Simpan items dan update stok
            foreach ($this->cart as $item) {
                $this->createTransactionItem($transaction, $item);
                $this->updateStock($item);
            }

            DB::commit();

            // Clear cart dari session
            session()->forget('cart');

            Notification::make()
                ->title('Pembayaran Berhasil!')
                ->body('Invoice: ' . $transaction->invoice_number)
                ->success()
                ->send();

            // Reset flag
            $this->isProcessing = false;

            // Redirect ke cashier page
            return redirect()->route('filament.admin.pages.cashier-page');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->isProcessing = false;
            
            Notification::make()
                ->title('Pembayaran Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function validateStock(): array
    {
        foreach ($this->cart as $item) {
            $productId = $item['product_id'];
            $variantId = $item['variant_id'] ?? null;
            $qty = $item['qty'];

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if (!$variant || $variant->stock < $qty) {
                    return [
                        'valid' => false,
                        'message' => "Stok {$item['name']} tidak mencukupi. Tersedia: " . ($variant->stock ?? 0)
                    ];
                }
            } else {
                $product = Product::find($productId);
                if (!$product || $product->base_stock < $qty) {
                    return [
                        'valid' => false,
                        'message' => "Stok {$item['name']} tidak mencukupi. Tersedia: " . ($product->base_stock ?? 0)
                    ];
                }
            }
        }

        return ['valid' => true];
    }

    private function updateStock(array $item): void
    {
        $productId = $item['product_id'];
        $variantId = $item['variant_id'] ?? null;
        $qty = $item['qty'];

        if ($variantId) {
            // Update stok variant
            ProductVariant::where('id', $variantId)
                ->decrement('stock', $qty);
        } else {
            // Update stok base product
            Product::where('id', $productId)
                ->decrement('base_stock', $qty);
        }
    }

    private function createTransactionItem(Transaction $transaction, array $item)
    {
        $productId = $item['product_id'];
        $variantId = $item['variant_id'] ?? null;

        // Ambil data SKU dan variant snapshot
        if ($variantId) {
            $variant = ProductVariant::with('product')->find($variantId);
            $sku = $variant->sku ?? null;
            $variantSnapshot = [
                'size' => $variant->size ?? null,
                'color' => $variant->color ?? null,
            ];
        } else {
            $product = Product::find($productId);
            $sku = $product->base_sku ?? null;
            $variantSnapshot = null;
        }

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'product_name_snapshot' => $item['name'],
            'sku_snapshot' => $sku,
            'variant_snapshot' => $variantSnapshot,
            'price' => $item['price'],
            'qty' => $item['qty'],
            'subtotal' => $item['price'] * $item['qty'],
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $lastTransaction = Transaction::whereDate('created_at', today())
            ->latest('id')
            ->first();

        $sequence = $lastTransaction ? (int) substr($lastTransaction->invoice_number, -4) + 1 : 1;

        return 'INV-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function goBack()
    {
        return redirect()->route('filament.admin.pages.cashier-page');
    }

    public function render()
    {
        return view('livewire.confirmation-page');
    }
}