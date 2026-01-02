<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;

class CheckoutPage extends Component
{   
    public array $cart = [];

    protected $listeners = [
        'cart:add' => 'addToCart',
    ];

    public function addToCart(int $productId, ?int $variantId = null)
    {
        $key = $this->makeKey($productId, $variantId);

        if ($variantId) {
            $variant = ProductVariant::with('product')->findOrFail($variantId);

            $name  = $variant->product->name;
            $price = $variant->price;
        } else {
            $product = Product::findOrFail($productId);

            $name  = $product->name;
            $price = $product->base_price;
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
        } else {
            $this->cart[$key] = [
                'key'        => $key,
                'product_id'=> $productId,
                'variant_id'=> $variantId,
                'name'      => $name,
                'price'     => $price,
                'qty'       => 1,
            ];
        }
    }

    public function increaseQty(string $key)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
        }
    }

    public function decreaseQty(string $key)
    {
        if (! isset($this->cart[$key])) {
            return;
        }

        if ($this->cart[$key]['qty'] > 1) {
            $this->cart[$key]['qty']--;
        } else {
            unset($this->cart[$key]);
        }
    }

    public function removeItem(string $key)
    {
        unset($this->cart[$key]);
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    private function makeKey(int $productId, ?int $variantId): string
    {
        return $productId . '-' . ($variantId ?? 'base');
    }

    public function goToConfirmation()
    {
        if (empty($this->cart)) {
            return;
        }

        session(['cart' => $this->cart]);

        return redirect()->route('filament.admin.pages.confirmation-page');
    }

    public function render()
    {
        return view('livewire.checkout-page');
    }
}
