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

    // Tidak wajib kalau view kamu pakai event token, tapi boleh disimpan.
    public ?string $snapToken = null;

    public function mount(): void
    {
        $this->cart = session('cart', []);

        if (empty($this->cart)) {
            redirect()->route('filament.admin.pages.cashier-page');
            return;
        }
    }

    public function confirmPayment(MidtransService $midtrans): void
    {
        if ($this->isProcessing) return;

        if (! auth()->check()) {
            Notification::make()
                ->title('Harus login dulu')
                ->danger()
                ->send();
            return;
        }

        $storeId = auth()->user()->currentStoreId();

        if (! $storeId) {
            Notification::make()
                ->title('Store belum dipilih')
                ->body('Silakan pilih / set current store terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang kosong')
                ->danger()
                ->send();
            return;
        }

        // Validasi: pastikan cart item hanya milik store ini & stok cukup
        $stock = $this->validateStock($storeId);
        if (! $stock['valid']) {
            Notification::make()
                ->title('Stok Tidak Cukup / Data Tidak Valid')
                ->body($stock['message'])
                ->danger()
                ->send();
            return;
        }

        // Gate subscription untuk midtrans
        if ($this->paymentMethod === 'midtrans') {
            $canMidtrans = auth()->user()->hasFeature('midtrans_payment');
            if (! $canMidtrans) {
                Notification::make()
                    ->title('Fitur Pro')
                    ->body('Pembayaran Midtrans hanya tersedia untuk paket Pro.')
                    ->danger()
                    ->send();
                return;
            }
        }

        $this->isProcessing = true;

        try {
            if ($this->paymentMethod === 'cash') {
                $this->processCashPayment($storeId);
            } else {
                $this->processMidtransPayment($storeId, $midtrans);
            }
        } finally {
            $this->isProcessing = false;
        }
    }

    private function processCashPayment(int $storeId): void
    {
        DB::transaction(function () use ($storeId) {
            $transaction = $this->createTransaction($storeId, 'cash', 'paid');

            foreach ($this->cart as $item) {
                $this->createTransactionItem($storeId, $transaction, $item);
                $this->updateStock($storeId, $item);
            }
        });

        session()->forget('cart');

        Notification::make()
            ->title('Pembayaran Tunai Berhasil')
            ->success()
            ->send();

        redirect()->route('filament.admin.pages.cashier-page');
    }

    private function processMidtransPayment(int $storeId, MidtransService $midtrans): void
    {
        try {
            $snapToken = DB::transaction(function () use ($storeId, $midtrans) {
                // 1) Buat transaksi pending
                $transaction = $this->createTransaction($storeId, 'midtrans', 'pending');

                // 2) Buat items (jangan update stok di sini)
                foreach ($this->cart as $item) {
                    $this->createTransactionItem($storeId, $transaction, $item);
                }

                // 3) Minta snap token dari Midtrans
                return $midtrans->createSnapToken($transaction);
            });

            $this->snapToken = $snapToken;

            // ✅ sesuai view fix: event kirim token langsung
            $this->dispatch('open-midtrans', token: $snapToken);

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Memproses Midtrans')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function createTransaction(int $storeId, string $method, string $status): Transaction
    {
        return Transaction::create([
            'store_id' => $storeId,
            'created_by' => auth()->id(), // boleh null kalau belum login, tapi kita sudah cek auth
            'invoice_number' => $this->generateInvoiceNumber(),
            'customer_name' => $this->customerName,
            'payment_method' => $method,
            'status' => $status,
            'total_items' => array_sum(array_column($this->cart, 'qty')),
            'total_amount' => collect($this->cart)->sum(fn ($i) => $i['price'] * $i['qty']),
            // 'paid_at' => now(), // jangan di sini; cash bisa diisi, midtrans di webhook
        ]);
    }

    private function createTransactionItem(int $storeId, Transaction $transaction, array $item): void
    {
        $variantId = $item['variant_id'] ?? null;

        // Validasi tambahan: pastikan product/variant milik store ini
        if ($variantId) {
            $variant = ProductVariant::with('product')
                ->where('store_id', $storeId)
                ->where('id', $variantId)
                ->first();

            if (! $variant) {
                throw new \RuntimeException("Variant tidak valid untuk store saat ini: {$variantId}");
            }

            $sku = $variant->sku;

            // Kamu sebelumnya pakai size/color, tapi di schema kamu ada pivot options.
            // Jadi snapshot variant kita simpan dari options biar sesuai struktur variant kamu.
            $variantSnapshot = $variant->options()
                ->with('type') // kalau ada relation type di VariantOption
                ->get()
                ->map(fn ($opt) => [
                    'type' => $opt->type->name ?? null,
                    'value' => $opt->value,
                ])
                ->values()
                ->all();

        } else {
            $product = Product::where('store_id', $storeId)
                ->where('id', $item['product_id'])
                ->first();

            if (! $product) {
                throw new \RuntimeException("Produk tidak valid untuk store saat ini: {$item['product_id']}");
            }

            $sku = $product->base_sku;
            $variantSnapshot = null;
        }

        TransactionItem::create([
            'store_id' => $storeId,                 // ✅ penting buat query reporting cepat
            'transaction_id' => $transaction->id,
            'product_id' => $item['product_id'],
            'product_variant_id' => $variantId,
            'product_name_snapshot' => $item['name'],
            'sku_snapshot' => $sku ?? '-',          // jaga-jaga null
            'variant_snapshot' => $variantSnapshot,
            'price' => $item['price'],
            'qty' => $item['qty'],
            'subtotal' => $item['price'] * $item['qty'],
        ]);
    }

    private function updateStock(int $storeId, array $item): void
    {
        $qty = (int) $item['qty'];

        if (! empty($item['variant_id'])) {
            ProductVariant::where('store_id', $storeId)
                ->where('id', $item['variant_id'])
                ->decrement('stock', $qty);
        } else {
            Product::where('store_id', $storeId)
                ->where('id', $item['product_id'])
                ->decrement('base_stock', $qty);
        }
    }

    private function validateStock(int $storeId): array
    {
        foreach ($this->cart as $item) {
            $qty = (int) ($item['qty'] ?? 0);

            if ($qty <= 0) {
                return ['valid' => false, 'message' => "Qty tidak valid untuk {$item['name']}"];
            }

            if (! empty($item['variant_id'])) {
                $variant = ProductVariant::where('store_id', $storeId)
                    ->where('id', $item['variant_id'])
                    ->first();

                if (! $variant) {
                    return ['valid' => false, 'message' => "Variant {$item['name']} tidak ditemukan di store ini"];
                }

                if (! $variant->is_active) {
                    return ['valid' => false, 'message' => "Variant {$item['name']} sedang nonaktif"];
                }

                if ($variant->stock < $qty) {
                    return ['valid' => false, 'message' => "Stok {$item['name']} tidak mencukupi"];
                }
            } else {
                $product = Product::where('store_id', $storeId)
                    ->where('id', $item['product_id'])
                    ->first();

                if (! $product) {
                    return ['valid' => false, 'message' => "Produk {$item['name']} tidak ditemukan di store ini"];
                }

                if (! $product->is_active) {
                    return ['valid' => false, 'message' => "Produk {$item['name']} sedang nonaktif"];
                }

                if ((int) $product->base_stock < $qty) {
                    return ['valid' => false, 'message' => "Stok {$item['name']} tidak mencukupi"];
                }
            }
        }

        return ['valid' => true, 'message' => null];
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
